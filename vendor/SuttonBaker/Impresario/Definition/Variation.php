<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Variation
 * @package SuttonBaker\Impresario\Definition
 */
class Variation
{
    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Variation';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Variation\Collection';
    const RECORDS_PER_PAGE = 20;

    const TABLE_HEADERS = [
        'variation_id' => 'ID',
        'date_approved' => 'Date Approved',
        'value' => 'Variation Amount'
    ];

}