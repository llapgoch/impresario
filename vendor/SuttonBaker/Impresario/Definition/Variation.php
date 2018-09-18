<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Variation
 * @package SuttonBaker\Impresario\Definition
 */
class Variation
{
    const API_ENDPOINT_UPDATE_TABLE = 'variation/updatetable';
    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Variation';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Variation\Collection';
    const RECORDS_PER_PAGE = 20;

    const STATUS_OPEN = 'open';
    const STATUS_APPROVED = 'approved';

    const TABLE_HEADERS = [
        'variation_id' => 'ID',
        'date_approved' => 'Date Approved',
        'value' => 'Variation Amount'
    ];

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_APPROVED => 'Approved'
        ];
    }

}