<?php

namespace SuttonBaker\Impresario\Controller\Invoice;

use DaveBaker\Core\Definitions\Messages;
use \SuttonBaker\Impresario\Definition\Invoice as InvoiceDefinition;
use SuttonBaker\Impresario\Helper\Invoice;


/**
 * Class EditController
 * @package SuttonBaker\Impresario\Controller\Task
 */
class EditController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const INVOICE_TYPE_PARAM = 'invoice_type';
    const PARENT_ID_PARAM = 'parent_id';
    const ENTITY_ID_PARAM = 'invoice_id';

    /** @var \DaveBaker\Form\Block\Form $editForm */
    protected $editForm;
    protected $parentItem;
    protected $invoiceType;
    protected $modelInstance;

    protected $nonUserValues = [
        'invoice_id',
        'created_by_id',
        'last_edited_by_id',
        'invoice_type',
        'parent_id',
        'created_at',
        'updated_at',
        'is_deleted'
    ];

    /**
     * @return \DaveBaker\Core\App\Response|object|\SuttonBaker\Impresario\Controller\Base
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _preDispatch()
    {

        // Set instance values before the blocks are created
        $invoiceType = $this->getRequest()->getParam(self::INVOICE_TYPE_PARAM);
        $parentId = $this->getRequest()->getParam(self::PARENT_ID_PARAM);
        $instanceId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM);

        $this->setModelInstance($this->getInvoiceHelper()->getInvoice());

        if(!$instanceId){
            $this->modelInstance->setTaskType($this->invoiceType);
            $this->modelInstance->setParentId($parentId);
        }



        if($instanceId){
            // We're loading, fellas!
            $this->modelInstance->load($instanceId);

            if(!$this->modelInstance->getId() || $this->modelInstance->getIsDeleted()){
                $this->addMessage('The invoice could not be found', Messages::ERROR);
                return $this->getResponse()->redirectReferer();
            }

        }else {
            if (!$this->getInvoiceHelper()->isValidInvoiceType($invoiceType)) {
                $this->addMessage('Invalid Invoice Type');
                $this->getResponse()->redirectReferer();
            }
        }

        $this->setParentItem($this->getParentItem($this->modelInstance));
        $this->setInvoiceType($this->getInvoiceHelper()->getInvoiceTypeForParent($this->parentItem));

        if(!$this->parentItem || !$this->parentItem->getId()){
            $this->addMessage('The parent item of the task could not be found');
            return $this->getResponse()->redirectReferer();
        }

        if(!$this->invoiceType){
            $this->addMessage('Invalid parent type');
            return $this->getResponse()->redirectReferer();
        }
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    public function execute()
    {
        if(!($this->editForm = $this->getApp()->getBlockManager()->getBlock('invoice.form.edit'))){
            return;
        }


        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');


        // Apply the values to the form element
        if($this->modelInstance->getId()) {
            $data = $this->modelInstance->getData();

            if($this->modelInstance->getInvoiceDate()){
                $data['invoice_date'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getInvoiceDate());
            }

            if($this->modelInstance->getValue()){
                $data['value'] = (float) $this->modelInstance->getValue();
            }

            $applicator->configure(
                $this->editForm,
                $data
            );
        }

        // Form submission
        if($this->getRequest()->getPostParam('action')){
            $postParams = $this->getRequest()->getPostParams();

            // Convert dates to DB
            if (isset($postParams['invoice_date'])){
                $postParams['invoice_date'] = $helper->localDateToDb($postParams['invoice_date']);
            }


            /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
            $configurator = $this->createAppObject('\SuttonBaker\Impresario\Form\InvoiceConfigurator');

            /** @var \DaveBaker\Form\Validation\Validator $validator */
            $validator = $this->createAppObject('\DaveBaker\Form\Validation\Validator')
                ->setValues($postParams)
                ->configurate($configurator);

            if(!$validator->validate()){
                return $this->prepareFormErrors($validator);
            }

            $this->saveFormValues($postParams);

            if(!$this->getApp()->getResponse()->redirectToReturnUrl()) {
                $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::PROJECT_LIST);
            }
        }


    }


    /**
     * @param $modelInstance
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function setModelInstance($modelInstance)
    {
        $this->modelInstance = $modelInstance;
        $this->getApp()->getRegistry()->register('model_instance', $modelInstance);
    }

    /**
     * @param $parentItem
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function setParentItem($parentItem)
    {
        $this->parentItem = $parentItem;
        $this->getApp()->getRegistry()->register('parent_item', $parentItem);
    }

    /**
     * @param string $taskType
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function setInvoiceType($invoiceType)
    {
        $this->invoiceType = $invoiceType;
        $this->getApp()->getRegistry()->register('invoice_type', $invoiceType);
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Invoice $instance
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry|\SuttonBaker\Impresario\Model\Db\Project|\SuttonBaker\Impresario\Model\Db\Quote
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getParentItem(\SuttonBaker\Impresario\Model\Db\Invoice $instance)
    {
        $taskType = null;
        $parentId = null;

        if($instance->getId()){
            $invoiceType = $instance->getInvoiceType();
            $parentId = $instance->getParentId();
        }else{
            $invoiceType = $this->getRequest()->getParam(self::INVOICE_TYPE_PARAM);
            $parentId = $this->getRequest()->getParam(self::PARENT_ID_PARAM);
        }

        if($invoiceType == InvoiceDefinition::INVOICE_TYPE_ENQUIRY){
            return $this->getEnquiryHelper()->getEnquiry($parentId);
        }

        if($invoiceType == InvoiceDefinition::INVOICE_TYPE_PROJECT){
            return $this->getProjectHelper()->getProject($parentId);
        }

        return null;
    }

    /**
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function saveFormValues($data)
    {
        if(!$this->getApp()->getHelper('User')->isLoggedIn()){
            return;
        }

        foreach($this->nonUserValues as $nonUserValue){
            if(isset($data[$nonUserValue])){
                unset($data[$nonUserValue]);
            }
        }

        // Add created by user
        if(!$this->modelInstance->getInvoiceId()) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
            $data['invoice_type'] = $this->invoiceType;
            $data['parent_id'] = $this->parentItem->getId();
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        $this->modelInstance->setData($data)->save();
        $this->addMessage(
            "The invoice has been " . ($this->modelInstance->getId() ? 'updated' : 'created'),
            Messages::SUCCESS
        );

        return $this;
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
            'invoice.edit.form.errors'
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