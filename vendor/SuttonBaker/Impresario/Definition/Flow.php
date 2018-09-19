<?php

namespace SuttonBaker\Impresario\Definition;

/**
 * Class Flow
 * @package SuttonBaker\Impresario\Definition
 */
class Flow
{
    const TABS = [
        'enquiry' => ['name' => 'Enquiry', 'href' => '#'],
        'quote' => ['name' => 'Quote', 'href' => '#'],
        'project' => ['name' => 'Project', 'href' => '#']
    ];

    /**
     * @return array
     */
    public static function getTabs()
    {
        return self::TABS;
    }
}