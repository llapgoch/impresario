<?php

namespace SuttonBaker\Impresario\Block\Enquiry\Form;

use DaveBaker\Core\Definitions\Table;
use \SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use \SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'enquiry_id';
    const PREFIX_KEY = 'enquiry';
    const PREFIX_NAME = 'Enquiry';

    /**
     * @return \SuttonBaker\Impresario\Block\Form\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\SelectConnector\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function _preDispatch()
    {
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $editMode = false;
        $quoteEntity = null;

        $entityInstance = $this->getApp()->getRegistry()->get('model_instance');

        $this->addClass('js-enquiry-form');
        $this->addAttribute(
            ['data-update-url' => $this->getApp()->getApiManager()->getUrl(
                'client/update',
                ['clientid' => $entityInstance->getId() ? $entityInstance->getId() : 0]
            )]
        );

        if($entityInstance->getId()){
            $editMode = true;
            $quoteEntity = $entityInstance->getQuoteEntity();
        }

        // Clients
        $clients = $this->createCollectionSelectConnector()
            ->configure(
                $this->getClientHelper()->getClientCollection(),
                'client_id',
                'client_name'
            )->getElementData();


        // PMs
        $assignedToUsers = $this->createCollectionSelectConnector()
            ->configure(
                $this->getApp()->getHelper('User')->getUserCollection(),
                'ID',
                'user_login'
            )->getElementData();

        // Engineers
        $engineers = $this->createCollectionSelectConnector()
            ->configure(
                $this->getApp()->getHelper('User')->getUserCollection(),
                'ID',
                'user_login'
            )->getElementData();

        // Completed Users
        $completedUsers = $this->createCollectionSelectConnector()
            ->configure(
                $this->getApp()->getHelper('User')->getUserCollection(),
                'ID',
                'user_login'
            )->getElementData();

        // Statuses
        $statuses = $this->createArraySelectConnector()->configure(EnquiryDefinition::getStatuses())->getElementData();

        $ignoreLockValue = false;

        if($this->getEnquiryHelper()->currentUserCanEdit()){
            $ignoreLockValue = true;
        }

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName('enquiry_edit')
            ->setGroupTemplate('form/group-vertical.phtml');

        $elements = $builder->build([
            [
                'name' => 'site_name',
                'labelName' => 'Site Name *',
                'type' => 'Input\Text',
                'formGroup' => true,
            ], [

                'name' => 'date_received',
                'labelName' => 'Date Received *',
                'formGroup' => true,
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly', 'autocomplete' => 'off'],
                'value' => $this->getDateHelper()->currentDateShortLocalOutput(),
                'rowIdentifier' => 'date_received_row',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'target_date',
                'labelName' => 'Target Date *',
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'formGroup' => true,
                'rowIdentifier' => 'date_received_row',
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '0', 'maxDate' => "+5Y"]
                    )
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'client_id',
                'labelName' => 'Client *',
                'formGroup' => true,
                'type' => 'Select',
                'rowIdentifier' => 'client_row_one',
                'data' => [
                    'select_options' => $clients
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'client_reference',
                'labelName' => 'Client Reference *',
                'formGroup' => true,
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off'],
                'rowIdentifier' => 'client_row_one',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'po_number',
                'labelName' => 'PO Number',
                'formGroup' => true,
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off'],
                'rowIdentifier' => 'po_mi_mw_numbers',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'mi_number',
                'labelName' => 'MI Number',
                'formGroup' => true,
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off'],
                'rowIdentifier' => 'po_mi_mw_numbers',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'nm_mw_number',
                'labelName' => 'NM/MW Number',
                'formGroup' => true,
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off'],
                'rowIdentifier' => 'po_mi_mw_numbers',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'assigned_to_id',
                'labelName' => 'Assigned To *',
                'type' => 'Select',
                'formGroup' => true,
                'rowIdentifier' => 'assigned_engineer',
                'data' => [
                    'select_options' => $assignedToUsers
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'engineer_id',
                'labelName' => 'Engineer',
                'type' => 'Select',
                'formGroup' => true,
                'rowIdentifier' => 'assigned_engineer',
                'class' => 'js-engineer',
                'data' => [
                    'select_options' => $engineers
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],[
                'name' => 'notes',
                'labelName' => 'Notes',
                'formGroup' => true,
                'type' => 'Textarea'
            ], [
                'name' => 'status',
                'labelName' => 'Enquiry Status *',
                'rowIdentifier' => 'creation_data',
                'formGroup' => true,
                'type' => 'Select',
                'show_first_option' => false,
                'class' => 'js-status',
                'data' => [
                    'select_options' => $statuses,
                    'show_first_option' => false,
                    'ignore_lock' => $ignoreLockValue
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
            ], [
                'name' => 'date_completed',
                'labelName' => 'Date Completed',
                'formGroup' => true,
                'class' => [
                    'js-date-picker',
                    'js-date-completed'
                ],
                'data' => ['ignore_lock' => $ignoreLockValue],
                'rowIdentifier' => 'creation_data',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'type' => 'Input\Text',
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '', 'maxDate' => "0"]
                    )]
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => [
                    'button_name' => $editMode ? 'Update Enquiry' : 'Create Enquiry',
                    'capabilities' => $this->getEnquiryHelper()->getEditCapabilities()
                ],
                'class' => 'btn-block'
            ], [
                'name' => 'enquiry_id',
                'type' => 'Input\Hidden',
                'value' => $entityInstance->getId()
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit',
                'data' => ['ignore_lock' => $ignoreLockValue]
            ], [
                'name' => 'enquiry_data',
                'type' => 'Input\Hidden',
                'value' => json_encode([
                    'hasQuote' => ($quoteEntity && $quoteEntity->getId() ? 1 : 0),
                    'completedStatus' => EnquiryDefinition::STATUS_COMPLETE
                ]),
                'class' => 'js-enquiry-data'
            ]
        ]);


        if($entityInstance->getId()) {
            $this->taskTableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\TableContainer',
                "{$prefixKey}.task.table"
            )->setOrder('after', 'enquiry.edit.notes.form.group')
                ->setCapabilities($this->getTaskHelper()->getViewCapabilities());


            $this->taskTableBlock->setInstanceCollection(
                $collection = $this->getTaskHelper()->getTaskCollectionForEntity(
                    $entityInstance->getId(),
                    TaskDefinition::TASK_TYPE_ENQUIRY,
                    TaskDefinition::STATUS_OPEN
                )

            )->setEditLinkParams([
                \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
            ]);


            $this->addChildBlock($this->taskTableBlock);
        }


        if(($entityInstance->getStatus() == EnquiryDefinition::STATUS_COMPLETE)){
            $this->addChildBlock(
                $this->createBlock(
                    '\SuttonBaker\Impresario\Block\Form\LargeMessage',
                    "{$prefixKey}.warning.message"
                )->setMessage("This {$prefixName} is currently locked")
            );
        }

        $this->addChildBlock(array_values($elements));


        if(($entityInstance->getStatus() == EnquiryDefinition::STATUS_COMPLETE) ||
            $this->getEnquiryHelper()->currentUserCanEdit() == false){
            $this->lock();
        }
    }

    /**
     * @return \SuttonBaker\Impresario\Block\Form\Base
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preRender()
    {

        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;
        $entityInstance = $this->getApp()->getRegistry()->get('model_instance');


        if($entityInstance->getId()) {
            if($tableBlock = $this->getBlockManager()->getBlock('task.table.list.table')) {
                $tableBlock->removeHeader(['status', 'task_id'])
                    ->addJsDataItems([
                        Table::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                            $this->getUrlHelper()->getApiUrl(
                                TaskDefinition::API_ENDPOINT_UPDATE_TABLE,
                                [
                                    'type' => TaskDefinition::TASK_TYPE_ENQUIRY,
                                    'parent_id' => $entityInstance->getId()
                                ]

                            ),
                    ]);

            }

            $addButton = $this->createBlock(
                '\DaveBaker\Core\Block\Html\Tag',
                'create.task.button',
                'header_elements'
            )->setTagText('Create Task')
                ->setTag('a')
                ->addAttribute(['href' => $this->getPageUrl(
                    \SuttonBaker\Impresario\Definition\Page::TASK_EDIT,
                    [
                        'task_type' => \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_ENQUIRY,
                        'parent_id' => $entityInstance->getId()
                    ],
                    true
                )])
                ->addClass('btn btn-sm btn-primary')
                ->setCapabilities($this->getTaskHelper()->getEditCapabilities());

            $this->getBlockManager()->getBlock('task.table.tile.block')
                ->addChildBlock($addButton);
        }



        return parent::_preRender(); // TODO: Change the autogenerated stub
    }


    /**
     * @return \DaveBaker\Form\SelectConnector\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createCollectionSelectConnector()
    {
        return $this->createAppObject('\DaveBaker\Form\SelectConnector\Collection');
    }

    /**
     * @return \DaveBaker\Form\SelectConnector\AssociativeArray
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createArraySelectConnector()
    {
        return $this->createAppObject('\DaveBaker\Form\SelectConnector\AssociativeArray');
    }
}