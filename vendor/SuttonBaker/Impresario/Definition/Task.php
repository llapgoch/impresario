<?php

namespace SuttonBaker\Impresario\Definition;
use DaveBaker\Core\Definitions\Table;

/**
 * Class Task
 * @package SuttonBaker\Impresario\Definition
 */
class Task
{
    const API_ENDPOINT_UPDATE_TABLE = 'task/updatetable';
    const API_ENDPOINT_DELETE = 'task/delete';
    const ICON = 'fa fa-th-list';

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
    const TASK_TYPE_PROJECT = 'project';

    const RECORDS_PER_PAGE = 20;
    const RECORDS_PER_PAGE_INLINE = 5;

    const TABLE_HEADERS = [
        'task_id' => 'ID',
        'created_by_name' => 'Creator',
        'status' => 'Status',
        'assigned_to_name' => 'Assignee',
        'task_type' => 'Type',
        'target_date' => 'Target',
        'priority' => 'Priority'
    ];

    const SORTABLE_COLUMNS = [
        'task_id' => [],
        'created_by_name' => [Table::HEADER_SORTABLE_ALPHA],
        'status' => [],
        'assigned_to_name' => [Table::HEADER_SORTABLE_ALPHA],
        'task_type' => [Table::HEADER_SORTABLE_ALPHA],
        'target_date' => [],
        'priority' => [Table::HEADER_SORTABLE_ALPHA]
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
    public static function getRowClasses()
    {
        return [
            self::PRIORITY_LOW => 'info',
            self::PRIORITY_MEDIUM => 'success',
            self::PRIORITY_HIGH => 'warning',
            self::PRIORITY_CRITICAL => 'danger'
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
            self::TASK_TYPE_QUOTE => 'Quote',
            self::TASK_TYPE_PROJECT => 'Project'
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