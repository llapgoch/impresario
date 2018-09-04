<?php

namespace SuttonBaker\Impresario\Block\Task\Form;

use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;

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

        $entityInstance = $this->getApp()->getRegistry()->get('model_instance');
        $parentItem = $this->getApp()->getRegistry()->get('parent_item');
        $taskType = $this->getApp()->getRegistry()->get('task_type');

        if($entityInstance->getId()){
            $editMode = true;
        }

        // PMs
        $assignedToUsers = $this->createCollectionSelectConnector()
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
        $statuses = $this->createArraySelectConnector()->configure(
            TaskDefinition::getStatuses()
        )->getElementData();

        // Priorities
        $priorities = $this->createArraySelectConnector()->configure(
            TaskDefinition::getPriorities()
        )->getElementData();

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit")->setGroupTemplate('form/group-vertical.phtml');

        $elements = $builder->build([
            [
                'name' => 'assigned_to_id',
                'labelName' => 'Assigned To *',
                'type' => 'Select',
                'rowIdentifier' => 'assigned_target_date',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'data' => [
                    'select_options' => $assignedToUsers
                ],
            ], [
                'name' => 'target_date',
                'labelName' => 'Target Date *',
                'class' => 'js-date-picker',
                'rowIdentifier' => 'assigned_target_date',
                'type' => 'Input\Text',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'value' => $this->getApp()->getHelper('Date')->utcDbDateToShortLocalOutput($parentItem->getTargetDate()),
                'attributes' => [
                    'readonly' => 'readonly',
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(['minDate' => '', 'maxDate' => "+5Y"])
                ],
            ], [
                'name' => 'description',
                'labelName' => 'Description *',
                'type' => 'Textarea',
                'formGroup' => true
            ], [
                'name' => 'notes',
                'labelName' => 'Notes',
                'type' => 'Textarea',
                'formGroup' => true
            ], [
                'name' => 'priority',
                'rowIdentifier' => 'priority_status',
                'labelName' => 'Priority *',
                'formGroup' => true,
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'data' => [
                    'select_options' => $priorities,
                    'show_first_option' => false
                ],

            ],[
                'name' => 'status',
                'rowIdentifier' => 'priority_status',
                'labelName' => 'Status *',
                'formGroup' => true,
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'data' => [
                    'select_options' => $statuses,
                    'show_first_option' => false

                ],

            ], [
                'name' => 'completed_by_id',
                'rowIdentifier' => 'completed_data',
                'formGroup' => true,
                'labelName' => 'Completed By',
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'data' => [
                    'select_options' => $completedUsers
                ],
            ], [
                'name' => 'date_completed',
                'rowIdentifier' => 'completed_data',
                'formGroup' => true,
                'labelName' => 'Date Completed',
                'type' => 'Select',
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'attributes' => ['autocomplete' => 'off']
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => ['button_name' => $editMode ? 'Update Task' : 'Create Task'],
                'class' => 'btn-block'
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
    }

}