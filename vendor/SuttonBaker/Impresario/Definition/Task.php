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

    public static function getTaskTypes()
    {
        return [
            self::TASK_TYPE_ENQUIRY => 'Enquiry',
            self::TASK_TYPE_QUOTE => 'quote'
        ];
    }

}