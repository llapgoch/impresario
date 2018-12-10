<?php

namespace SuttonBaker\Impresario\Api;
use DaveBaker\Core\Api\Exception;
use DaveBaker\Core\Block\Components\Paginator;
use DaveBaker\Core\Definitions\Messages;
use DaveBaker\Form\Block\Error\Main;
use DaveBaker\Form\Validation\Validator;
use SuttonBaker\Impresario\Block\Table\StatusLink;
use SuttonBaker\Impresario\Definition\Page;
use SuttonBaker\Impresario\Definition\Roles;

use SuttonBaker\Impresario\Form\QuoteConfigurator;
use SuttonBaker\Impresario\SaveConverter\Quote as QuoteConverter;
use SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use SuttonBaker\Impresario\Definition\Task as TaskDefinition;

/**
 * Class Quote
 * @package SuttonBaker\Impresario\Api
 *
 */
class Quote
    extends Base
{
    const ACTION_EDIT = 'edit';
    const ACTION_REVISE = 'revise';

    /** @var string  */
    protected $blockPrefix = 'quote';
    /** @var array  */
    protected $capabilities = [Roles::CAP_VIEW_QUOTE];

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function validatesaveAction(
        $params,
        \WP_REST_Request $request
    ) {
        $helper = $this->getQuoteHelper();
        $confirmMessages = [];

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['formValues'])){
            throw new Exception('No form values provided');
        }

        $navigatingAway = isset($params['navigatingAway']) && $params['navigatingAway'] ? true : false;
        $converter = $this->createAppObject(QuoteConverter::class);
        $formValues = $converter->convert($params['formValues']);
        /** @var \SuttonBaker\Impresario\Model\Db\Quote */
        $modelInstance = $this->loadQuote($formValues);
        $validateResult = $this->validateValues($modelInstance, $formValues);

        if($validateResult['hasErrors']){
            return $validateResult;
        }

        if($helper->saveQuoteCreateProjectCheck($modelInstance, $formValues)){
            $confirmMessages[] = sprintf(
                'This will %screate a new project for the quote.',
                $confirmMessages ? 'also ' : ''
            );

            $openTasks = $helper->getTasksForQuote($modelInstance, TaskDefinition::STATUS_OPEN);
        
            if(count($openTasks->getItems())){
                $confirmMessages[] = sprintf(
                    'This will close %s open task%s for the quote.',
                    count($openTasks->getItems()),
                    count($openTasks->getItems()) > 1 ? 's' : ''
                );
            }
        }

        if($modelInstance->getTenderStatus() !== QuoteDefinition::TENDER_STATUS_OPEN
            && $formValues['tender_status'] == QuoteDefinition::TENDER_STATUS_OPEN) {
            $confirmMessages[] = 'This will re-open the quote.';
        }
        
       
        if($confirmMessages){
            $confirmMessages[] = "Would you like to proceed?";
            $validateResult['confirm'] = implode("\r", $confirmMessages);

            return $validateResult;
        }

        return array_merge($validateResult, $this->saveQuote($modelInstance, $formValues, $navigatingAway));
    }

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function createRevisionAction(
        $params,
        \WP_REST_Request $request
    ) {
        $helper = $this->getQuoteHelper();

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['id'])){
            throw new Exception('Quote ID not set');
        }

        $quote = $helper->getQuote($params['id']);

        if(!$quote->getId()){
            throw new Exception('The quote could not be found');
        }

        $newQuote = $helper->duplicateQuote($quote);

        return [
          'redirect' => $this->getUrlHelper()->getUrl(
              Page::QUOTE_EDIT, [
                    'quote_id' => $newQuote->getId()
              ]
        )];
    }

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function saveAction(
        $params,
        \WP_REST_Request $request
    ) {
        $helper = $this->getQuoteHelper();

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['formValues'])){
            throw new Exception('No form values provided');
        }

        $navigatingAway = isset($params['navigatingAway']) && $params['navigatingAway'] ? true : false;
        $converter = $this->createAppObject(QuoteConverter::class);
        $formValues = $converter->convert($params['formValues']);
        $modelInstance = $this->loadQuote($formValues);

        $validateResult = $this->validateValues($modelInstance, $formValues);

        if($validateResult['hasErrors']){
            return $validateResult;
        }

        $saveResult = $this->saveQuote($modelInstance, $formValues, $navigatingAway);

        return $saveResult;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $modelInstance
     * @param $formValues
     * @return array
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    protected function validateValues(
        \SuttonBaker\Impresario\Model\Db\Quote $modelInstance,
        $formValues
    ) {
        $blockManager = $this->getApp()->getBlockManager();
        $helper = $this->getQuoteHelper();
        $saveResult = [];

        /** @var QuoteConfigurator $configurator */
        $configurator = $this->createAppObject(QuoteConfigurator::class);
        /** @var Validator $validator */
        $validator = $this->createAppObject(Validator::class)->setValues($formValues);
        $validator->configurate($configurator)->validate();

        $errorBlock = $blockManager->createBlock(Main::class, 'quote.edit.form.errors');
        $errorBlock->addErrors($validator->getErrors())->setIsReplacerBlock(true);

        $this->addReplacerBlock($errorBlock);

        return [
            'hasErrors' => $validator->hasErrors(),
            'errorFields' => $validator->getErrorFields()
        ];
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $modelInstance
     * @param $formValues
     * @param bool $navigatingAway
     * @return array
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function saveQuote(
        \SuttonBaker\Impresario\Model\Db\Quote $modelInstance,
        $formValues,
        $navigatingAway = false
    ) {
        $saveValues = [];

        if(isset($formValues['action']) && $formValues['action'] == self::ACTION_REVISE) {
            $saveValues = $this->getQuoteHelper()->reviseQuote($modelInstance, $formValues);
            $saveValues['revised'] = true;
        } else {
            $saveValues = $this->getQuoteHelper()->saveQuote($modelInstance, $formValues);
            $saveValues['revised'] = false;
        }

        if ($saveValues['new_save'] == true) {
            $this->getApp()->getGeneralSession()->addMessage(
                "The quote has been created",
                Messages::SUCCESS
            );
        }

        // No need to redirect for updating
        if ($saveValues['new_save'] == false
            && !$saveValues['project_created'] && !$saveValues['reopened'] && !$saveValues['revised']) {
            $message = 'The quote has been updated';

            if ($navigatingAway) {
                $this->getApp()->getGeneralSession()->addMessage(
                    $message,
                    Messages::SUCCESS
                );
            } else {
                $this->addReplacerBlock(
                    $this->getModalHelper()->createAutoOpenModal(
                        'Success',
                        $message
                    )
                );
            }
        }

        // Redirect to the new quote or quote edit page
        if ($saveValues['new_save'] && !$saveValues['revised']) {
            $saveValues['redirect'] = $this->getUrlHelper()->getPageUrl(
                Page::QUOTE_EDIT,
                ['quote_id' => $saveValues['quote_id']]
            );
        }

        if($saveValues['revised']) {
            $saveValues['redirect'] = $this->getUrlHelper()->getPageUrl(
                Page::QUOTE_EDIT,
                ['quote_id' => $saveValues['quote_id']]
            );

            $this->getApp()->getGeneralSession()->addMessage(
                'The quote has been revised',
                Messages::SUCCESS
            );
        }

        // Redirect to new project
        if ($saveValues['project_created']) {
            $this->getApp()->getGeneralSession()->addMessage(
                'A new project has been created for the quote',
                Messages::SUCCESS
            );

            $saveValues['redirect'] = $this->getUrlHelper()->getPageUrl(
                Page::PROJECT_EDIT,
                ['project_id' => $saveValues['project_id']]
            );
        }

        if ($saveValues['reopened']) {
            $saveValues['redirect'] = $this->getUrlHelper()->getPageUrl(
                Page::QUOTE_EDIT,
                ['quote_id' => $saveValues['quote_id']]
            );

            $this->getApp()->getGeneralSession()->addMessage(
                'The quote has been re-opened',
                Messages::SUCCESS
            );
        }

        return $saveValues;
    }

    /**
     * @param $params
     * @return \SuttonBaker\Impresario\Model\Db\Quote
     * @throws Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function loadQuote($params)
    {
        $modelInstance = $this->getQuoteHelper()->getQuote();

        if(isset($params['quote_id']) && $params['quote_id']){
            $modelInstance->load($params['quote_id']);

            if(!$modelInstance->getId()){
                throw new Exception('The quote could not be found');
            }
        }

        return $modelInstance;
    }

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function updatetableAction($params, \WP_REST_Request $request)
    {
        $blockManager = $this->getApp()->getBlockManager();

        /** @var StatusLink $tableBlock */
        $tableBlock = $blockManager->getBlock("{$this->blockPrefix}.list.table");

        if(isset($params['order']['dir']) && isset($params['order']['column'])){
            $tableBlock->setColumnOrder($params['order']['column'], $params['order']['dir']);
        }

        /** @var Paginator $paginatorBlock */
        $paginatorBlock = $blockManager->getBlock("{$this->blockPrefix}.list.paginator");

        if(isset($params['pageNumber'])){
            $paginatorBlock->setPage($params['pageNumber']);
        }

        $this->addReplacerBlock([$tableBlock, $paginatorBlock]);
    }

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @throws Exception
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function updaterevisiontableAction(
        $params, \WP_REST_Request $request
    ) {
        $helper = $this->getQuoteHelper();
        $blockManager = $this->getApp()->getBlockManager();

        if(!isset($params['quote_id'])){
            throw new Exception('Quote ID must be provided');
        }

        $quote = $helper->getQuote($params['quote_id']);

        if(!$quote->getId()){
            throw new Exception('The quote could not be found');
        }

        $revisions = $this->getQuoteHelper()->getQuotesForEnquiry($quote->getEnquiryId());

        $blockManager->createBlock(
            \SuttonBaker\Impresario\Block\Quote\RevisionsTableContainer::class,
            "{$this->blockPrefix}.past.revisions.table"
        )->setCapabilities($this->getQuoteHelper()->getViewCapabilities())
            ->setRevisions($revisions)
            ->setQuote($quote)
            ->preDispatch();

        $tableBlock = $blockManager->getBlock("{$this->blockPrefix}.revision.list.table");

        if(isset($params['order']['dir']) && isset($params['order']['column'])){
            $tableBlock->setColumnOrder($params['order']['column'], $params['order']['dir']);
        }

        /** @var Paginator $paginatorBlock */
        $paginatorBlock = $blockManager->getBlock("{$this->blockPrefix}.revision.list.paginator");

        if(isset($params['pageNumber'])){
            $paginatorBlock->setPage($params['pageNumber']);
        }

        $this->addReplacerBlock([$tableBlock, $paginatorBlock]);
    }

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function deleteAction($params, \WP_REST_Request $request)
    {
        /** @var \SuttonBaker\Impresario\Helper\Quote $helper */
        $helper = $this->getQuoteHelper();

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['id'])){
            throw new Exception('The item could not be found');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Quote $item */
        $item = $this->createAppObject(
            \SuttonBaker\Impresario\Definition\Quote::DEFINITION_MODEL
        )->load($params['id']);

        if(!$item->getId()){
            throw new Exception('The item could not be found');
        }

        $helper->deleteQuote($item);
        $this->addMessage('The quote has been removed', Messages::SUCCESS);

        return true;
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Quote
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getQuoteHelper()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\Quote::class);
    }

}