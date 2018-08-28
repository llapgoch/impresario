<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Definition
 */
class Enquiry
{
    const STATUS_OPEN = 'open';
    const STATUS_COMPLETE = 'complete';
    const STATUS_ON_HOLD  = 'on_hold';
    const STATUS_IN_PROGRESS = 'in_progress';

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_COMPLETE => 'Complete'
        ];
    }
}