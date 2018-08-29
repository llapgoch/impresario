<?php

namespace SuttonBaker\Impresario\Block\Enquiry\Form;

use \SuttonBaker\Impresario\Definition\Enquiry;

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
     * @return \DaveBaker\Form\Block\Form|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\SelectConnector\Exception
     */
    protected function _preDispatch()
    {
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $heading = "Create {$prefixName}";
        $editMode = false;

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            /** @var \SuttonBaker\Impresario\Model\Db\Enquiry $entityInstance */
            $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Enquiry')->load($entityId);
            $heading = "Update {$prefixName}";
            $editMode = true;

            /** @var \SuttonBaker\Impresario\Model\Db\Enquiry\Collection $tasks */
            $taskInstance = $this->getTaskHelper()->getTaskCollectionForEntity(
                \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_ENQUIRY
            );

            $taskItems = $taskInstance->load();
            $headers = count($taskItems) ? array_keys($taskItems[0]->getData()) : [];

            $this->addChildBlock(
                $this->createBlock('\DaveBaker\Core\Block\Html\Tag', 'create.task')
                ->setTag('a')
                ->setTagText('New Task')
                ->addAttribute(
                    ['href' => $this->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::TASK_EDIT,
                        [
                            'task_type' => \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_ENQUIRY,
                            'parent_id' => $entityId
                        ]
                    )]
                )
            );


            $this->addChildBlock(
                $this->createBlock('\DaveBaker\Core\Block\Html\Table', "{$prefixKey}.task.table")
                    ->setHeaders($headers)->setRecords($taskItems)->addEscapeExcludes(
                        ['edit_column', 'delete_column']
                )
            );
        }

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Core\Block\Html\Heading', "{$prefixKey}.form.edit.heading")
                ->setHeading($heading)
        );

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName('enquiry_edit');

        $elements = $builder->build([
            [
                'name' => 'date_received',
                'labelName' => 'Date Received',
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly', 'autocomplete' => 'false']
            ], [
                'name' => 'client_reference',
                'labelName' => 'Client Reference',
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off']
            ], [
                'name' => 'client_id',
                'labelName' => 'Client',
                'type' => 'Select'
            ], [
                'name' => 'project_manager_id',
                'labelName' => 'Project Manager',
                'type' => 'Select'
            ], [
                'name' => 'engineer_id',
                'labelName' => 'Engineer',
                'type' => 'Select'
            ], [
                'name' => 'site_name',
                'labelName' => 'Site Name',
                'type' => 'Input\Text'
            ], [
                'name' => 'notes',
                'labelName' => 'Notes',
                'type' => 'TextArea'
            ], [
                'name' => 'status',
                'labelName' => 'Enquiry Status',
                'type' => 'Select'
            ], [
                'name' => 'completed_by_id',
                'labelName' => 'Completed By',
                'type' => 'Select'
            ], [
                'name' => 'target_date',
                'labelName' => 'Target Date',
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => [
                    'readonly' => 'readonly',
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '', 'maxDate' => "+5Y"]
                    )]
            ], [
                'name' => 'submit',
                'type' => 'Input\Submit',
                'value' => $editMode ? 'Update Enquiry' : 'Create Enquiry'
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


        // Set up special values

        // Client
        $clients = $this->getClientHelper()->getClientCollection();
        $this->createCollectionSelectConnector()
            ->configure($clients, 'client_id', 'client_name', $elements['client_id_element']);


        // Project Manager
        $projectManagers = $this->getApp()->getHelper('User')->getUserCollection();
        $this->createCollectionSelectConnector()
            ->configure($projectManagers, 'ID', 'user_login', $elements['project_manager_id_element']);

        // Engineer
        $engineers = $this->getApp()->getHelper('User')->getUserCollection();
        $this->createCollectionSelectConnector()
            ->configure($engineers, 'ID', 'user_login', $elements['engineer_id_element']);

        // Completed by Users
        $completedUsers = $this->getApp()->getHelper('User')->getUserCollection();
        $this->createCollectionSelectConnector()
            ->configure($completedUsers, 'ID', 'user_login', $elements['completed_by_id_element']);

        // Statuses
        $this->createArraySelectConnector()
            ->configure(Enquiry::getStatuses(), $elements['status_element']);

        $elements['status_element']->setShowFirstOption(false);


        $this->addChildBlock(array_values($elements));
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