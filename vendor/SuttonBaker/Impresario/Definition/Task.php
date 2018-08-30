<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Task
 * @package SuttonBaker\Impresario\Definition
 */
class Task
{
    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Task';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Task\Collection';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH  = 'high';
    const PRIORITY_CRITICAL = 'critical';

    const STATUS_OPEN = 'open';
    const STATUS_COMPLETE = 'complete';

    const TASK_TYPE_ENQUIRY = 'enquiry';
    const TASK_TYPE_QUOTE = 'quote';

    const TABLE_HEADERS = [
        'task_id' => 'ID',
        'created_by_name' => 'Created By',
        'status' => 'Status',
        'assigned_to_name' => 'Assigned To',
        'task_type' => 'Type',
        'target_date' => 'Target Date',
        'priority' => 'Priority',
        'edit_column' => '',
        'delete_column' => ''
    ];

    /**
     * @return array
     */
    public static function getPriorities()
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_CRITICAL => 'Critical'
        ];
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_COMPLETE => 'Complete'
        ];
    }

    /**
     * @return array
     */
    public static function getTaskTypes()
    {
        return [
            self::TASK_TYPE_ENQUIRY => 'Enquiry',
            self::TASK_TYPE_QUOTE => 'Quote'
        ];
    }

    /**
     * @param string $taskType
     * @return string
     */
    public static function getTaskTypeLabel($taskType)
    {
        $taskTypes = self::getTaskTypes();

        if(in_array($taskType, array_keys($taskTypes))){
            return $taskTypes[$taskType];
        }

        return '';
    }

}