<?php

namespace SuttonBaker\Impresario\Helper;

use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
/**
 * Class Task
 * @package SuttonBaker\Impresario\Helper
 */
class Task extends \DaveBaker\Core\Helper\Base
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

        $collection->getSelect()->where('is_deleted=?', '0');

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

    public function getTaskTypeForParent($parentInstance)
    {
        if($parentInstance instanceof \SuttonBaker\Impresario\Model\Db\Enquiry){
            return TaskDefinition::TASK_TYPE_ENQUIRY;
        }

        if($parentInstance instanceof \SuttonBaker\Impresario\Model\Db\Quote){
            return TaskDefinition::TASK_TYPE_QUOTE;
        }
    }
}