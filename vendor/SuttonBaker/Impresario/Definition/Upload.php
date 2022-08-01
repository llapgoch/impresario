<?php

namespace SuttonBaker\Impresario\Definition;

/**
 * Class Upload
 * @package SuttonBaker\Impresario\Definition
 */
class Upload
{
    const API_ENDPOINT_UPDATE_TABLE = 'upload/updatetable';
    const TYPE_ENQUIRY = 'enquiry';
    const TYPE_ENQUIRY_TEST = 'enquiry_test';
    const TYPE_QUOTE = 'quote';
    const TYPE_PROJECT = 'project';
    const TYPE_PROJECT_COMPLETION_CERTIFICATE = 'project_comp_cert';
    const TYPE_TASK = 'task';
    const TYPE_VARIATION = 'variation';
    const TYPE_INVOICE = 'invoice';
    const TYPE_COST = 'cost';
    const RECORDS_PER_PAGE = 10;

    const TABLE_HEADERS = [
        'icon' => '',
        'filename' => 'Attachment Name',
        'created_by_name' => 'Uploaded By',
        'remove' => ''
    ];

    public static function getIcon($mimeType) {
        $iconClasses = array(
            // Media
            'image' => 'fa-file-image-o',
            'audio' => 'fa-file-audio-o',
            'video' => 'fa-file-video-o',
            // Documents
            'application/pdf' => 'fa-file-pdf-o',
            'application/msword' => 'fa-file-word-o',
            'application/vnd.ms-word' => 'fa-file-word-o',
            'application/vnd.oasis.opendocument.text' => 'fa-file-word-o',
            'application/vnd.openxmlformats-officedocument.wordprocessingml' => 'fa-file-word-o',
            'application/vnd.ms-excel' => 'fa-file-excel-o',
            'application/vnd.openxmlformats-officedocument.spreadsheetml' => 'fa-file-excel-o',
            'application/vnd.oasis.opendocument.spreadsheet' => 'fa-file-excel-o',
            'application/vnd.ms-powerpoint' => 'fa-file-powerpoint-o',
            'application/vnd.openxmlformats-officedocument.presentationml' => 'fa-file-powerpoint-o',
            'application/vnd.oasis.opendocument.presentation' => 'fa-file-powerpoint-o',
            'text/plain' => 'fa-file-text-o',
            'text/html' => 'fa-file-code-o',
            'application/json' => 'fa-file-code-o',
            // Archives
            'application/gzip' => 'fa-file-archive-o',
            'application/zip' => 'fa-file-archive-o',
        );

        foreach ($iconClasses as $text => $icon) {
            if (strpos($mimeType, $text) === 0) {
                return 'fa ' . $icon;
            }
        }

        return 'fa fa-file-o';
    }
}