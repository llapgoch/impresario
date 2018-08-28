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
}