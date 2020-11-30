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
        'value' => 'Cost Amount',
        'supplier_name' => 'Supplier Name',
        'cost_invoice_type' => 'Type'
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
    public static function getCostInvoiceTypes($visibleOnly = false)
    {
        $items = [
            self::COST_INVOICE_TYPE_LABOUR => 'Labour',
            self::COST_INVOICE_TYPE_PLANT => 'Plant',
            self::COST_INVOICE_TYPE_MATERIAL => 'Material',
            self::COST_INVOICE_TYPE_SUBCONTRACTOR => 'Sub-contractor'
        ];

        if($visibleOnly === false) {
            $items[self::COST_INVOICE_TYPE_MIGRATION_INITIAL] = 'Migration';
        }

        return $items;
    }

}