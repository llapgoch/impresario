<?php

namespace SuttonBaker\Impresario\Helper;

use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
/**
 * Class Task
 * @package SuttonBaker\Impresario\Helper
 */
class Task extends Base
{

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Task\Collection
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Returns a collection of non-deleted tasks
     */
    public function getTaskCollection()
    {
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Task\Collection'
        );

        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);
        $collection->getSelect()->where('is_deleted=?', '0');

        $collection->joinLeft(
            $userTable,
            "{$userTable}.ID={{task}}.assigned_to_id",
            ['assigned_to_name' => 'user_login']
        );

        $collection->joinLeft(
            $userTable,
            "{$userTable}.ID={{task}}.created_by_id",
            ['created_by_name' => 'user_login']
        );

        return $collection;
    }

    /**
     * @param string $entity
     * @param string $status
     * @return \SuttonBaker\Impresario\Model\Db\Task\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getTaskCollectionForEntity($entityId, $entity, $status = '')
    {
        $collection = $this->getTaskCollection();

        $collection->getSelect()->where('task_type=?', $entity);
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