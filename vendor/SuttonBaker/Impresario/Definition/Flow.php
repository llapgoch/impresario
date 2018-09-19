<?php

namespace SuttonBaker\Impresario\Definition;

/**
 * Class Flow
 * @package SuttonBaker\Impresario\Definition
 */
class Flow
{
    const TABS = [
        'enquiry' => [
            'name' => 'Enquiry',
            'href' => '#',
            'icon' => Enquiry::ICON
        ],
        'quote' => [
            'name' => 'Quote',
            'href' => '#',
            'icon' => Quote::ICON
        ],
        'project' => [
            'name' => 'Project',
            'href' => '#',
            'icon' => Project::ICON
        ]
    ];

    /**
     * @return array
     */
    public static function getTabs()
    {
        return self::TABS;
    }
}