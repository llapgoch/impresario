<?php

namespace SuttonBaker\Impresario\Block\Client\Form;
use \SuttonBaker\Impresario\Definition\Client as ClientDefinition;
use SuttonBaker\Impresario\Definition\Page;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'client_id';
    const PREFIX_KEY = 'client';
    const PREFIX_NAME = 'Client';

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

        $heading = 'Create a New Client';
        $editMode = false;
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $entityInstance = $this->getApp()->getRegistry()->get('model_instance');

        if($entityInstance->getId()){
            $editMode = true;
        }

        if($clientId = $this->getRequest()->getParam('client_id')){
            $client = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Client')->load($clientId);
            $heading = "Update '{$client->getClientName()}'";
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
            $this->getUrlHelper()->getPageUrl(Page::CLIENT_LIST);

        $elements = $builder->build([
            [
                'name' => 'client_name',
                'labelName' => 'Client Name *',
                'type' => 'Input\Text',
                'formGroup' => true,
                'rowIdentifier' => 'client_name',
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
                    'button_name' => $this->getClientHelper()->getActionVerb($entityInstance) . " Client",
                    'capabilities' => $this->getClientHelper()->getEditCapabilities()
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
                    'button_name' => 'Remove Client',
                    'capabilities' => $this->getClientHelper()->getEditCapabilities(),
                    'js_data_items' => [
                        'type' => 'Client',
                        'endpoint' => $this->getUrlHelper()->getApiUrl(
                            ClientDefinition::API_ENDPOINT_DELETE,
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

        $this->addChildBlock(array_values($elements));

        if($this->getClientHelper()->currentUserCanEdit() == false){
            $this->lock();
        }

    }
}