<?php

namespace SuttonBaker\Impresario\Block\Supplier\Form;
use \SuttonBaker\Impresario\Definition\Supplier as SupplierDefinition;
use SuttonBaker\Impresario\Definition\Page;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Supplier\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'supplier_id';
    const PREFIX_KEY = 'supplier';
    const PREFIX_NAME = 'Supplier';

    /**
     * @return \DaveBaker\Form\Block\Form|void
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();
        $this->addClass('js-loader');

        $heading = 'Create a New Supplier';
        $editMode = false;
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $entityInstance = $this->getApp()->getRegistry()->get('model_instance');

        if($entityInstance->getId()){
            $editMode = true;
        }

        if($supplierId = $this->getRequest()->getParam('supplier_id')){
            $supplier = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Supplier')->load($supplierId);
            $heading = "Update '{$supplier->getSupplierName()}'";
            $editMode = true;
        }

        /** @var \DaveBaker\Core\Model\Db\Directory\Country $countryCollection */
        $countryCollection = $this->createAppObject('\DaveBaker\Core\Model\Db\Directory\Country\Collection');
        /** @var \DaveBaker\Form\SelectConnector\Collection $selectConnector */
        $countries = $this->createAppObject('\DaveBaker\Form\SelectConnector\Collection')
            ->configure($countryCollection, 'country_code', 'country_name')->getElementData();


        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit")->setGroupTemplate('form/group-vertical.phtml');

        $disabledAttrs = $entityInstance->getId() ? [] : ['disabled' => 'disabled'];
        $returnUrl = $this->getRequest()->getReturnUrl() ?
            $this->getRequest()->getReturnUrl() :
            $this->getUrlHelper()->getPageUrl(Page::SUPPLIER_LIST);

        $elements = $builder->build([
            [
                'name' => 'supplier_name',
                'labelName' => 'Supplier Name *',
                'type' => 'Input\Text',
                'formGroup' => true,
                'rowIdentifier' => 'supplier_name',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ],  [
                'name' => 'address_line1',
                'labelName' => 'Address Line 1 *',
                'type' => 'Input\Text',
                'formGroup' => true
            ], [
                'name' => 'address_line2',
                'labelName' => 'Address Line 2',
                'type' => 'Input\Text',
                'rowIdentifier' => 'addresses',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'address_line3',
                'labelName' => 'Address Line 3',
                'type' => 'Input\Text',
                'rowIdentifier' => 'addresses',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'postcode',
                'rowIdentifier' => 'postcode_country',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
                'labelName' => 'Postcode *',
                'type' => 'Input\Text'
            ], [
                'name' => 'county',
                'rowIdentifier' => 'postcode_country',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
                'labelName' => 'County *',
                'type' => 'Input\Text'
            ], [
                'name' => 'country_code',
                'rowIdentifier' => 'postcode_country',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
                'labelName' => 'Country *',
                'type' => 'Select',
                'value' => $this->getApp()->getHelper('Directory')->getDefaultCountryCode(),
                'data' => [
                    'select_options' => $countries
                ]
            ], [
                'name' => 'sales_contact_phone',
                'rowIdentifier' => 'sales_contact',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'labelName' => 'Sales Phone Number *',
                'type' => 'Input\Text'
            ], [
                'name' => 'sales_contact',
                'labelName' => 'Sales Contact Name *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'sales_contact',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],

            ],[
                'name' => 'accounts_contact_phone',
                'labelName' => 'Accounts Phone Number *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'accounts_contact',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
            ], [
                'name' => 'accounts_contact',
                'labelName' => 'Accounts Contact Name *',
                'type' => 'Input\Text',
                'formGroup' => true,
                'rowIdentifier' => 'accounts_contact',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
            ], [
                'name' => 'submit',
                'rowIdentifier' => 'button_bar',
                'formGroup' => true,
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => [
                    'button_name' => $this->getSupplierHelper()->getActionVerb($entityInstance, false) . " Supplier",
                    'capabilities' => $this->getSupplierHelper()->getEditCapabilities()
                ],
                'class' => 'btn-block',
                'formGroupSettings' => [
                    'class' => 'col-md-8'
                ]

            ], [
                'name' => 'delete_button',
                'rowIdentifier' => 'button_bar',
                'type' => '\DaveBaker\Form\Block\Button',
                'formGroup' => true,
                'attributes' => $disabledAttrs,
                'data' => [
                    'button_name' => 'Remove Supplier',
                    'capabilities' => $this->getSupplierHelper()->getEditCapabilities(),
                    'js_data_items' => [
                        'type' => 'Supplier',
                        'endpoint' => $this->getUrlHelper()->getApiUrl(
                            SupplierDefinition::API_ENDPOINT_DELETE,
                            ['id' => $entityInstance->getId()]
                        ),
                        'returnUrl' => $returnUrl
                    ]
                ],
                'class' => 'btn-block btn-danger js-delete-confirm',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'task_id',
                'type' => 'Input\Hidden',
                'value' => $entityInstance->getId()
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ]
        ]);

        $this->addRecordMonitorBlock(
            $entityInstance,
            $this->getUrlHelper()->getApiUrl(SupplierDefinition::API_ENDPOINT_RECORD_MONITOR)
        );

        $this->addChildBlock(array_values($elements));

        if($this->getSupplierHelper()->currentUserCanEdit() == false){
            $this->lock();
        }

    }
}