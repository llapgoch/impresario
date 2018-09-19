<?php

namespace SuttonBaker\Impresario\Controller\Quote;

use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class EditController
 * @package SuttonBaker\Impresario\Controller\Quote
 */
class EditController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const PARENT_ID_PARAM = 'parent_id';
    const ENTITY_ID_PARAM = 'quote_id';
    const ENQUIRY_ID_PARAM = 'enquiry_id';

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_QUOTE,
        Roles::CAP_VIEW_QUOTE,
        Roles::CAP_ALL
    ];

    /** @var \DaveBaker\Form\Block\Form $editForm */
    protected $editForm;
    /** @var \SuttonBaker\Impresario\Model\Db\Quote */
    protected $parentItem;
    /** @var \SuttonBaker\Impresario\Model\Db\Enquiry */
    protected $enquiryItem;
    /** @var \SuttonBaker\Impresario\Model\Db\Quote */
    protected $modelInstance;

    /** @var array  */
    protected $nonUserValues = [
        'quote_id',
        'created_by_id',
        'last_edited_by_id',
        'enquiry_id',
        'parent_id',
        'created_at',
        'updated_at',
        'is_deleted'
    ];


    /**
     * @return bool|\SuttonBaker\Impresario\Controller\Base
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {

        if(!($instanceId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM))){
            return $this->getResponse()->redirectReferer(
                $this->getUrlHelper()->getPageUrl(Page::QUOTE_LIST)
            );
        }

        $this->modelInstance = $this->getQuoteHelper()->getQuote($instanceId);

        if(!$this->modelInstance->getId()){
            $this->addMessage('The quote could not be found');

            $this->redirectToPage(Page::QUOTE_LIST);
        }
    }

    /**
     * @return \DaveBaker\Core\App\Response|object
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function execute()
    {
        if(!($this->editForm = $this->getApp()->getBlockManager()->getBlock('quote.form.edit'))){
            return;
        }

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        wp_register_script('impresario_calculator', get_template_directory_uri() . '/assets/js/profit-calculator.js', ['jquery']);
        wp_enqueue_script('impresario_calculator');

        wp_register_script('impresario_quote', get_template_directory_uri() . '/assets/js/quote-edit.js', ['jquery']);
        wp_enqueue_script('impresario_quote');

        // Form submission
        if($this->getRequest()->getPostParam('action')){
            $postParams = $this->modifyFormValuesForSave($this->getRequest()->getPostParams());

            /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
            $configurator = $this->createAppObject('\SuttonBaker\Impresario\Form\QuoteConfigurator');

            /** @var \DaveBaker\Form\Validation\Validator $validator */
            $validator = $this->createAppObject('\DaveBaker\Form\Validation\Validator')
                ->setValues($postParams)
                ->configurate($configurator);

            if(!$validator->validate()){
                return $this->prepareFormErrors($validator);
            }

            $this->saveFormValues($postParams);
        }

        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');
        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        // Apply the values to the form element
        if($this->modelInstance->getId()) {
            $data = $this->modelInstance->getData();

            if($this->modelInstance->getDateReturned()){
                $data['date_returned'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateReturned());
            }

            if($this->modelInstance->getTargetDate()){
                $data['target_date'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getTargetDate());
            }

            if($this->modelInstance->getDateCompleted()){
                $data['date_completed'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateCompleted());
            }

            if($this->modelInstance->getDateReceived()){
                $data['date_received'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateReceived());
            }

            if($this->modelInstance->getDateRequired()){
                $data['date_required'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateRequired());
            }

            if($this->modelInstance->getDateReturnBy()){
                $data['date_return_by'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateReturnBy());
            }

            if($this->modelInstance->getNetSell()){
                $data['net_sell'] = (float) $this->modelInstance->getNetSell();
            }

            if($this->modelInstance->getNetSell()){
                $data['net_cost'] = (float) $this->modelInstance->getNetCost();
            }

            $applicator->configure(
                $this->editForm,
                $data
            );
        }
    }

    protected function modifyFormValuesForSave($postParams)
    {
        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        // Convert dates to DB
        if (isset($postParams['date_received'])){
            $postParams['date_received'] = $helper->localDateToDb($postParams['date_received']);
        }

        if(isset($postParams['date_required'])){
            $postParams['date_required'] = $helper->localDateToDb($postParams['date_required']);
        }

        if(isset($postParams['date_return_by'])){
            $postParams['date_return_by'] = $helper->localDateToDb($postParams['date_return_by']);
        }

        if(isset($postParams['date_returned'])){
            $postParams['date_returned'] = $helper->localDateToDb($postParams['date_returned']);
        }

        if(isset($postParams['date_completed'])){
            $postParams['date_completed'] = $helper->localDateToDb($postParams['date_completed']);
        }

        return $postParams;
    }

    /**
     * @param $data
     * @return \DaveBaker\Core\App\Response|null
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     */
    protected function saveFormValues($data)
    {
        if(!$this->getApp()->getHelper('User')->isLoggedIn()){
            return;
        }

        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        foreach($this->nonUserValues as $nonUserValue){
            if(isset($data[$nonUserValue])){
                unset($data[$nonUserValue]);
            }
        }


        // Add created by user
        if(!$this->modelInstance->getQuoteId()) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
        $messageSet = false;

        // Check if we need to create a new quote
        if((float) $this->modelInstance->getNetCost() && (float) $this->modelInstance->getNetSell()) {
            if (((float)$this->modelInstance->getNetCost() !== (float)$data['net_cost'] ||
                (float)$this->modelInstance->getNetSell() !== (float)$data['net_sell'])
            ) {
                $this->modelInstance = $this->getQuoteHelper()->duplicateQuote($this->modelInstance);
                $messageSet = true;

                $this->addMessage(
                    "The quote has been revised based on changes to Net Sell or Net Cost",
                    Messages::SUCCESS
                );
            }
        }


        $this->modelInstance->setData($data)->save();

        // Create a project


        if($data['status'] == QuoteDefinition::STATUS_WON) {
            $project = $this->getProjectHelper()->getProjectForQuote($this->modelInstance->getId());

            if (!$project->getId()) {

                $project = $this->getProjectHelper()->createProjectFromQuote($this->modelInstance->getId());
                $messageSet = true;

                $this->addMessage(
                    "A new project has been created for the quote",
                    Messages::SUCCESS
                );

                return $this->redirectToPage(
                    \SuttonBaker\Impresario\Definition\Page::PROJECT_EDIT,
                    ['project_id' => $project->getId()]
                );
            }
        }

        if(!$messageSet) {
            $this->addMessage(
                "The quote has been " . ($this->modelInstance->getId() ? 'updated' : 'created'),
                Messages::SUCCESS
            );
        }

        if(!$this->getApp()->getResponse()->redirectToReturnUrl()) {
            $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::QUOTE_LIST);
        }
    }

    /**
     * @param \DaveBaker\Form\Validation\Validator $validator
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     */
    protected function prepareFormErrors(
        \DaveBaker\Form\Validation\Validator $validator
    ) {
        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Create main error block
        /** @var \DaveBaker\Form\Block\Error\Main $errorBlock */
        $errorBlock = $this->getApp()->getBlockManager()->createBlock(
            '\DaveBaker\Form\Block\Error\Main',
            'quote.edit.form.errors'
        )->setOrder('before', '')->addErrors($validator->getErrors());

        $this->editForm->addChildBlock($errorBlock);

        // Sets the values back onto the form element
        $applicator->configure(
            $this->editForm,
            $this->getRequest()->getPostParams(),
            $validator
        );
    }
}