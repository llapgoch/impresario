<?php

namespace SuttonBaker\Impresario\Block\Enquiry\Form;

use \SuttonBaker\Impresario\Definition\Enquiry;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;

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
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\SelectConnector\Exception
     * @throws \Zend_Db_Select_Exception
     */
    protected function _preDispatch()
    {
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;
        $entityId = $this->getRequest()->getParam(self::ID_KEY);
        $editMode = false;


        if($entityId){
            /** @var \SuttonBaker\Impresario\Model\Db\Enquiry $entityInstance */
            $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Enquiry')->load($entityId);

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
        $projectManagers = $this->createCollectionSelectConnector()
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
        $statuses = $this->createArraySelectConnector()->configure(Enquiry::getStatuses())->getElementData();

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName('enquiry_edit')->setGroupTemplate('form/group-vertical.phtml');

        $elements = $builder->build([
            [
                'name' => 'date_received',
                'labelName' => 'Date Received *',
                'formGroup' => true,
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly', 'autocomplete' => 'off'],
                'value' => $this->getApp()->getHelper('Date')->currentDateShortLocalOutput(),
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
                'name' => 'site_name',
                'labelName' => 'Site Name *',
                'type' => 'Input\Text',
                'formGroup' => true,
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
            ],[
                'name' => 'project_manager_id',
                'labelName' => 'Project Manager *',
                'type' => 'Select',
                'formGroup' => true,
                'rowIdentifier' => 'pm_engineer',
                'data' => [
                    'select_options' => $projectManagers
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'engineer_id',
                'labelName' => 'Engineer *',
                'type' => 'Select',
                'formGroup' => true,
                'rowIdentifier' => 'pm_engineer',
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
                'type' => 'TextArea'
            ], [
                'name' => 'status',
                'labelName' => 'Enquiry Status *',
                'formGroup' => true,
                'type' => 'Select',
                'show_first_option' => false,
                'data' => [
                    'select_options' => $statuses
                ]
            ], [
                'name' => 'completed_by_id',
                'labelName' => 'Completed By',
                'formGroup' => true,
                'type' => 'Select',
                'data' => [
                    'select_options' => $completedUsers
                ]
            ], [
                'name' => 'date_completed',
                'labelName' => 'Date Completed',
                'formGroup' => true,
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '', 'maxDate' => "0"]
                    )]
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => ['button_name' => $editMode ? 'Update Enquiry' : 'Create Enquiry'],
                'class' => 'btn-block'
            ], [
                'name' => 'enquiry_id',
                'type' => 'Input\Hidden',
                'value' => $entityId
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ]
        ]);

        if($entityId) {
            $this->taskTableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\TaskTable',
                "{$prefixKey}.task.table"
            )->setOrder('after', 'enquiry.edit.notes.form.group');

            $this->taskTableBlock->setInstanceCollection(
                $this->getTaskHelper()->getTaskCollectionForEntity(
                    $entityId,
                    TaskDefinition::TASK_TYPE_ENQUIRY,
                    TaskDefinition::STATUS_OPEN
                )
            )->setEditLinkParams([
                \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
            ]);


            $this->addChildBlock($this->taskTableBlock);
        }


        $this->addChildBlock(array_values($elements));
    }

    /**
     * @return \SuttonBaker\Impresario\Block\Form\Base
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preRender()
    {

        $entityId = $this->getRequest()->getParam(self::ID_KEY);
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;


        if($entityId) {
            $tableBlock = $this->getBlockManager()->getBlock(
                'task.list.table')->removeHeader(['delete_column', 'status', 'task_id']);

            $addButton = $tableBlock->createBlock(
                '\DaveBaker\Core\Block\Html\Tag',
                'create.task.button',
                'header_elements'
            )->setTagText('Create Task')
                ->setTag('a')
                ->addAttribute(['href' => $this->getPageUrl(
                    \SuttonBaker\Impresario\Definition\Page::TASK_EDIT,
                    [
                        'task_type' => \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_ENQUIRY,
                        'parent_id' => $entityId
                    ],
                    true
                )])
                ->addClass('btn btn-sm btn-primary');

            $this->getBlockManager()->getBlock('task.tile.block')
                ->addChildBlock($addButton);
        }

        return parent::_preRender(); // TODO: Change the autogenerated stub
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Client
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getClientHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Client');
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