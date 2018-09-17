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
    const TYPE_TASK = 'task';

    const TABLE_HEADERS = [
        'filename' => 'Attachment Name',
        'created_by_name' => 'Uploaded By'
    ];

    const ICONS = [
        'jpg' => 'fa-file-image-o'
    ];
}