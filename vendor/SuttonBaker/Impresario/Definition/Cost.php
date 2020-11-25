<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Cost
 * @package SuttonBaker\Impresario\Definition
 */
class Cost
{
    const API_ENDPOINT_UPDATE_TABLE = 'cost/updatetable';
    const API_ENDPOINT_DELETE = 'cost/delete';
    const API_ENDPOINT_RECORD_MONITOR = 'cost/recordmonitor';
    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Cost';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Cost\Collection';
    const ICON = 'fa fa-gbp';

    const COST_TYPE_PROJECT = 'project';

    const RECORDS_PER_PAGE = 20;

    const TABLE_HEADERS = [
        'cost_id' => 'ID',
        'cost_date' => 'Date',
        'cost_number' => 'Number',
        'value' => 'Cost Amount'
    ];

    /**
     * @return array
     */
    public static function getCostTypes()
    {
        return [
            self::COST_TYPE_PROJECT => 'Project'
        ];
    }

}