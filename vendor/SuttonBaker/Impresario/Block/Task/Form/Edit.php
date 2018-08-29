<?php

namespace SuttonBaker\Impresario\Block\Task\Form;

use \SuttonBaker\Impresario\Definition\Task;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'task_id';
    const PREFIX_KEY = 'task';
    const PREFIX_NAME = 'Task';

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
            $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Task')->load($entityId);
            $heading = "Update {$prefixName}";
            $editMode = true;
        }

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Core\Block\Html\Heading', "{$prefixKey}.form.edit.heading")
                ->setHeading($heading)
        );

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit");

        $elements = $builder->build([
            [
                'name' => 'assigned_to_id',
                'labelName' => 'Assigned To',
                'type' => 'Select'
            ], [
                'name' => 'target_date',
                'labelName' => 'Target Date',
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => [
                    'readonly' => 'readonly',
                    'autocomplete' => 'false',
                    'data-date-settings' => json_encode(['minDate' => '', 'maxDate' => "+5Y"])
                ],
            ], [
                'name' => 'description',
                'labelName' => 'Description',
                'type' => 'TextArea'
            ], [
                'name' => 'notes',
                'labelName' => 'Notes',
                'type' => 'TextArea'
            ], [
                'name' => 'status',
                'labelName' => 'Status',
                'type' => 'Select'
            ], [
                'name' => 'completed_by_id',
                'labelName' => 'Completed By',
                'type' => 'Select'
            ], [
                'name' => 'completed_date',
                'labelName' => 'Completed Date',
                'type' => 'Select',
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly', 'autocomplete' => 'false']
            ], [
                'name' => 'submit',
                'type' => 'Input\Submit',
                'value' => $editMode ? 'Update Task' : 'Create Task'
            ], [
                'name' => 'task_id',
                'type' => 'Input\Hidden',
                'value' => $entityId
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ]
        ]);

        // Set up special values

        // Assigned To
        $assignedToUsers = $this->getApp()->getHelper('User')->getUserCollection();
        $this->createCollectionSelectConnector()
            ->configure($assignedToUsers , 'ID', 'user_login', $elements['assigned_to_id_element']);

        // Completed by Users
        $completedUsers = $this->getApp()->getHelper('User')->getUserCollection();
        $this->createCollectionSelectConnector()
            ->configure($completedUsers, 'ID', 'user_login', $elements['completed_by_id_element']);

        // Statuses
        $this->createArraySelectConnector()
            ->configure(Task::getStatuses(), $elements['status_element']);

        $elements['status_element']->setShowFirstOption(false);
        $this->addChildBlock(array_values($elements));
    }

}