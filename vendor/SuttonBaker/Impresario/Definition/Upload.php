<?php

namespace SuttonBaker\Impresario\Definition;

/**
 * Class Upload
 * @package SuttonBaker\Impresario\Definition
 */
class Upload
{
    const TYPE_ENQUIRY = 'enquiry';
    const TYPE_QUOTE = 'quote';
    const TYPE_PROJECT = 'project';

    const TABLE_HEADERS = [
        'icon' => '',
        'filename' => 'File Name',
        'created_by_name' => 'Uploaded By'
    ];
}