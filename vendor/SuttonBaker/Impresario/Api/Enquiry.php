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
use SuttonBaker\Impresario\Form\EnquiryConfigurator;
use SuttonBaker\Impresario\SaveConverter\Enquiry as EnquiryConverter;
use SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;

/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Api
 *
 */
class Enquiry
    extends Base
{
    /** @var string  */
    protected $blockPrefix = 'enquiry';
    /** @var array  */
    protected $capabilities = [Roles::CAP_VIEW_ENQUIRY];

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function validatesaveAction(
        $params,
        \WP_REST_Request $request
    ) {
        $helper = $this->getEnquiryHelper();

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['formValues'])){
            throw new Exception('No form values provided');
        }

        $modelInstance = $helper->getEnquiry();
        $converter = $this->createAppObject(EnquiryConverter::class);
        $formValues = $converter->convert($params['formValues']);

        if(isset($formValues['enquiry_id']) && $formValues['enquiry_id']){
            $modelInstance->load($formValues['enquiry_id']);

            if(!$modelInstance->getId()){
                throw new Exception('The enquiry could not be found');
            }
        }

        $validateResult = $this->validateValues($modelInstance, $formValues);

        if($validateResult['hasErrors']){
            return $validateResult;
        }

        // Check whether a new quote will be created for this enquiry
        if(isset($formValues['status'])
            && $formValues['status'] == EnquiryDefinition::STATUS_COMPLETE){
            $quote = $this->getQuoteHelper()->getNewestQuoteForEnquiry($modelInstance->getId());

            if(!$quote->getId()){
                $validateResult['confirm'] = 'A new quote will be created for this enquiry, are you sure you want to proceed?';
                return $validateResult;
            }
        }

        return array_merge($validateResult, $this->saveEnquiry($modelInstance, $formValues));
    }

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @return array|\SuttonBaker\Impresario\Helper\Enquiry|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function saveAction($params, \WP_REST_Request $request)
    {
        $helper = $this->getEnquiryHelper();

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['formValues'])){
            throw new Exception('No form values provided');
        }

        $modelInstance = $helper->getEnquiry();
        $converter = $this->createAppObject(EnquiryConverter::class);
        $formValues = $converter->convert($params['formValues']);

        if(isset($formValues['enquiry_id']) && $formValues['enquiry_id']){
            $modelInstance->load($formValues['enquiry_id']);

            if(!$modelInstance->getId()){
                throw new Exception('The enquiry could not be found');
            }
        }

        $converter = $this->createAppObject(EnquiryConverter::class);
        $helper = $this->getEnquiryHelper();
        $formValues = $converter->convert($params['formValues']);

        $validateResult = $this->validateValues($modelInstance, $formValues);

        if($validateResult['hasErrors']){
            return $validateResult;
        }

        $saveResult = $this->saveEnquiry($modelInstance, $formValues);

        return $saveResult;
    }

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @throws \DaveBaker\Core\Block\Exception
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
     * @return bool|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function deleteAction(
        $params,
        \WP_REST_Request $request
    ) {
        $helper = $this->getEnquiryHelper();
        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['id'])){
            throw new Exception('The item could not be found');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Enquiry $item */
        $item = $this->createAppObject(
            \SuttonBaker\Impresario\Definition\Enquiry::DEFINITION_MODEL
        )->load($params['id']);

        if(!$item->getId()){
            throw new Exception('The item could not be found');
        }

        $helper->deleteEnquiry($item);
        $this->addMessage('The enquiry has been removed', Messages::SUCCESS);

        return true;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Enquiry $modelInstance
     * @param $formValues
     * @return array
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    protected function validateValues(
        \SuttonBaker\Impresario\Model\Db\Enquiry $modelInstance,
        $formValues
    ) {
        $blockManager = $this->getApp()->getBlockManager();
        $helper = $this->getEnquiryHelper();
        $saveResult = [];

        /** @var EnquiryConfigurator $configurator */
        $configurator = $this->createAppObject(EnquiryConfigurator::class);
        /** @var Validator $validator */
        $validator = $this->createAppObject(Validator::class)->setValues($formValues);
        $validator->configurate($configurator)->validate();

        $errorBlock = $blockManager->createBlock(Main::class, 'enquiry.edit.form.errors');
        $errorBlock->addErrors($validator->getErrors())->setIsReplacerBlock(true);

        $this->addReplacerBlock($errorBlock);

        return [
            'hasErrors' => $validator->hasErrors(),
            'errorFields' => $validator->getErrorFields()
        ];
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Enquiry $modelInstance
     * @param $formValues
     * @return array|\SuttonBaker\Impresario\Helper\Enquiry
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    protected function saveEnquiry(
        \SuttonBaker\Impresario\Model\Db\Enquiry $modelInstance,
        $formValues
    ) {
        $saveValues = $this->getEnquiryHelper()->saveEnquiry($modelInstance, $formValues);

        if($saveValues['newSave'] == false && !$saveValues['quoteCreated']){
            $this->addReplacerBlock(
                $this->getModalHelper()->createAutoOpenModal(
                    'Success',
                    'The enquiry has been updated'
                )
            );
        }

        if($saveValues['quoteCreated']){
            $this->getApp()->getGeneralSession()->addMessage(
                'A new quote has been created for the enquiry',
                Messages::SUCCESS
            );

            $saveValues['redirect'] = $this->getUrlHelper()->getPageUrl(
                Page::QUOTE_EDIT,
                ['quote_id' => $saveValues['quoteId']]
            );
        }

        if(!$saveValues['quoteCreated'] && $saveValues['newSave']){
            $this->getApp()->getGeneralSession()->addMessage(
                "The enquiry has been created",
                Messages::SUCCESS
            );
        }

        return $saveValues;
    }



}