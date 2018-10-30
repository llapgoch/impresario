<?php

namespace SuttonBaker\Impresario\Helper;

use SuttonBaker\Impresario\Definition\Roles;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;

/**
 * Class Task
 * @package SuttonBaker\Impresario\Helper
 */
class Task extends Base
{
    /** @var array  */
    protected $editCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_TASK];
    protected $viewCapabilities = [Roles::CAP_ALL, Roles::CAP_VIEW_TASK, Roles::CAP_EDIT_TASK];

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Task\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getTaskCollection()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Task\Collection $collection */
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Task\Collection'
        );

        /** @var \Zend_Db_Select $select */
        $select = $collection->getSelect();
        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);

        $select->where('is_deleted=?', '0');

        $collection->joinLeft(
            ['assigned_to_user' => $userTable],
            "assigned_to_user.ID={{task}}.assigned_to_id",
            ['assigned_to_name' => 'user_login'])
        ->joinLeft(
            ['created_by_user'=> $userTable],
            "created_by_user.ID={{task}}.created_by_id",
            ['created_by_name' => 'user_login'])
          ->order(new \Zend_Db_Expr(sprintf(
                "FIELD({{task}}.status,'%s', '%s')",
                TaskDefinition::STATUS_OPEN,
                TaskDefinition::STATUS_COMPLETE
                )
            )
        )
        ->order(new \Zend_Db_Expr(sprintf(
            "FIELD({{task}}.priority,'%s', '%s', '%s', '%s')",
            TaskDefinition::PRIORITY_CRITICAL,
            TaskDefinition::PRIORITY_HIGH,
            TaskDefinition::PRIORITY_MEDIUM,
            TaskDefinition::PRIORITY_LOW)
        ))->order('{{task}}.target_date');


        return $collection;
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Task\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getOpenTasks()
    {
        return $this->getTaskCollection()->where('status=?', TaskDefinition::STATUS_OPEN);
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Task\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getOpenTasksForCurrentUser()
    {
        return $this->getOpenTasks()->where('user_id=?', $this->getUserHelper()->getCurrentUserId());
    }

    /**
     * @param $entityId
     * @param $entityType
     * @param string $status
     * @return \SuttonBaker\Impresario\Model\Db\Task\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getTaskCollectionForEntity($entityId, $entityType, $status = '')
    {
        $collection = $this->getTaskCollection();

        $collection->getSelect()->where('task_type=?', $entityType);
        $collection->getSelect()->where('parent_id=?', $entityId);

        if($status) {
            $collection->getSelect()->where('status=?', $status);
        }

        return $collection;
    }

    /**
     * @param $status
     * @return string
     */
    public function getStatusDisplayName($status)
    {
        return $this->getDisplayName($status, TaskDefinition::getStatuses());
    }

    /**
     * @param string $taskType
     * @return mixed|string
     */
    public function getTaskTypeDisplayName($taskType)
    {
        return $this->getDisplayName($taskType, TaskDefinition::getTaskTypes());
    }

    /**
     * @param $priority
     * @return mixed|string
     */
    public function getPriorityDisplayName($priority)
    {
        return $this->getDisplayName($priority, TaskDefinition::getPriorities());
    }

    /**
     * @param $taskType
     * @return bool
     */
    public function isValidTaskType($taskType)
    {
        return in_array($taskType, array_keys(\SuttonBaker\Impresario\Definition\Task::getTaskTypes()));
    }

    /**
     * @param $status
     * @return bool
     */
    public function isValidStatus($status)
    {
        return in_array($status, array_keys(\SuttonBaker\Impresario\Definition\Task::getStatuses()));
    }

    /**
     * @param $priority
     * @return bool
     */
    public function isValidPriority($priority)
    {
        return in_array($priority, array_keys(\SuttonBaker\Impresario\Definition\Task::getPriorities()));
    }

    /**
     * @param $enquiryId
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getTask($taskId = null)
    {
        $task = $this->createAppObject(TaskDefinition::DEFINITION_MODEL);

        if($taskId){
            $task->load($taskId);
        }

        return $task;
    }

    /**
     *
     * @param \SuttonBaker\Impresario\Model\Db\Task\Collection $collection
     * @return \SuttonBaker\Impresario\Model\Db\Task\Collection
     */
    public function addOutputProcessorsToCollection(
        \SuttonBaker\Impresario\Model\Db\Task\Collection $collection
    ) {
        $collection->addOutputProcessors([
            'updated_at' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'target_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'status' => $this->getTaskHelper()->getStatusOutputProcessor(),
            'task_type' => $this->getTaskHelper()->getTaskTypeOutputProcessor(),
            'priority' => $this->getTaskHelper()->getPriorityOutputProcessor()
        ]);

        return $collection;
    }

    /**
     * @param $parentInstance
     * @return string
     */
    public function getTaskTypeForParent($parentInstance)
    {
        if($parentInstance instanceof \SuttonBaker\Impresario\Model\Db\Enquiry){
            return TaskDefinition::TASK_TYPE_ENQUIRY;
        }

        if($parentInstance instanceof \SuttonBaker\Impresario\Model\Db\Quote){
            return TaskDefinition::TASK_TYPE_QUOTE;
        }

        if($parentInstance instanceof \SuttonBaker\Impresario\Model\Db\Project){
            return TaskDefinition::TASK_TYPE_PROJECT;
        }

        return null;
    }

    /**
     * @param $parentInstance
     * @return string
     */
    public function getLinkForParent($parentInstance)
    {
        // This assumes the edit page is the same as the task type, with an _edit suffix
        if(($type = $this->getTaskTypeForParent($parentInstance))){
            return $this->getApp()->getPageManager()->getUrl(
                $type . "_edit",
                [$type . "_id" => $parentInstance->getId()]
            );
        }

        return null;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Task $task
     * @return $this
     */
    public function deleteTask(
        \SuttonBaker\Impresario\Model\Db\Task $task
    ) {
        if(!$task->getId()){
            return $this;
        }

        $task->setIsDeleted(1)->save();
        return $this;
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\OutputProcessor\Quote\Status
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getStatusOutputProcessor()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\OutputProcessor\Task\Status');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\OutputProcessor\Quote\Status
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getTaskTypeOutputProcessor()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\OutputProcessor\Task\Type');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\OutputProcessor\Quote\Status
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getPriorityOutputProcessor()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\OutputProcessor\Task\Priority');
    }
}