<?php

namespace SuttonBaker\Impresario\Definition;

class Priority
{
    const PRIORITY_LOW = 1000;
    const PRIORITY_MEDIUM = 2000;
    const PRIORITY_HIGH = 3000;
    const PRIORITY_URGENT = 6000;
    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_MEDIUM => 'Medium',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }
}
