<?php

namespace SuttonBaker\Impresario\Controller\Supplier;
use DaveBaker\Core\Definitions\Messages;
use \SuttonBaker\Impresario\Definition\Page;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class SupplierEditController
 * @package SuttonBaker\Impresario\Controller\Supplier
 */
class EditController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    /** @var \DaveBaker\Form\Block\Form $supplierEditForm */
    protected $supplierEditForm;
    protected $modelInstance;

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_SUPPLIER,
        Roles::CAP_VIEW_SUPPLIER,
        Roles::CAP_ALL
    ];

    protected $nonUserValues = [
        'supplier_id',
        'created_by_id',
        'last_edited_by_id',
        'created_at',
        'updated_at',
        'is_deleted'
    ];

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    public function _preDispatch()
    {

        $this->modelInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Supplier');
        $this->getApp()->getRegistry()->register('model_instance', $this->modelInstance);


        if($supplierId = (int) $this->getRequest()->getParam('supplier_id')){
            $this->modelInstance->load($supplierId);

            if(!$this->modelInstance->getId()){
                $this->addMessage('The supplier does not exist');
                $this->redirectToPage(Page::SUPPLIER_LIST);
            }
        }


    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function saveFormValues($data)
    {
        foreach($this->nonUserValues as $nonUserValue){
            if(isset($data[$nonUserValue])){
                unset($data[$nonUserValue]);
            }
        }

        // Add created by user
        if(!$data['supplier_id']) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();


        $this->addMessage(
            "The supplier '{$data["supplier_name"]}' has been " . ($this->modelInstance->getId() ? 'updated' : 'created'),
            Messages::SUCCESS
        );

        $this->modelInstance->setData($data)->save();

        return $this;
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    public function execute()
    {
        /** @var \DaveBaker\Form\Block\Form $supplierEditForm */
        if(!($this->supplierEditForm = $this->getApp()->getBlockManager()->getBlock('supplier.form.edit'))){
            return;
        }

        // Form submission
        if($this->getRequest()->getPostParam('action')){

            $postParams = $this->getRequest()->getPostParams();

            /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
            $configurator = $this->createAppObject('\SuttonBaker\Impresario\Form\SupplierConfigurator');

            /** @var \DaveBaker\Form\Validation\Validator $validator */
            $validator = $this->createAppObject('\DaveBaker\Form\Validation\Validator')
                ->setValues($postParams)
                ->configurate($configurator);

            if(!$validator->validate()){
                return $this->prepareFormErrors($validator);
            }

            $supplierName = $this->getRequest()->getPostParam('supplier_name');
            $this->saveFormValues($postParams);
            $this->redirectToPage(Page::SUPPLIER_LIST);
        }

        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Apply the values to the form element
        if($this->modelInstance->getId()) {
            $applicator->configure(
                $this->supplierEditForm,
                $this->modelInstance->getData()
            );
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
            'supplier.edit.form.errors'
        )->setOrder('before', '')->addErrors($validator->getErrors());

        $this->supplierEditForm->addChildBlock($errorBlock);

        // Sets the values back onto the form element
        $applicator->configure(
            $this->supplierEditForm,
            $this->getRequest()->getPostParams(),
            $validator
        );
    }
}