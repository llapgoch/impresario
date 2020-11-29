<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Cost
 * @package SuttonBaker\Impresario\Definition
 */
class Cost
{
    const COST_NAME = 'name';
    const COST_VALUE = 'value';
    const COST_VISIBLE = 'visible';

    const API_ENDPOINT_UPDATE_TABLE = 'cost/updatetable';
    const API_ENDPOINT_DELETE = 'cost/delete';
    const API_ENDPOINT_RECORD_MONITOR = 'cost/recordmonitor';
    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Cost';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Cost\Collection';
    const ICON = 'fa fa-gbp';

    const COST_INVOICE_TYPE_LABOUR = 'labour';
    const COST_INVOICE_TYPE_PLANT = 'plant';
    const COST_INVOICE_TYPE_MATERIAL = 'material';
    const COST_INVOICE_TYPE_SUBCONTRACTOR = 'subcontractor';
    const COST_INVOICE_TYPE_MIGRATION_INITIAL = 'm_initial';

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

    /**
     *
     * @return array
     */
    public static function getVisibleCostInvoiceTypes()
    {
        return [
            [self::COST_VALUE => self::COST_INVOICE_TYPE_LABOUR, self::COST_NAME => 'Labour'],
            [self::COST_VALUE => self::COST_INVOICE_TYPE_PLANT, self::COST_NAME => 'Plant'],
            [self::COST_VALUE => self::COST_INVOICE_TYPE_MATERIAL, self::COST_NAME => 'Material'],
            [self::COST_VALUE => self::COST_INVOICE_TYPE_SUBCONTRACTOR, self::COST_NAME => 'Sub-contractor'],
        ];
    }

}