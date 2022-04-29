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
    const API_ENDPOINT_VALIDATE_SAVE = 'cost/validatesave';

    const NON_USER_VALUES = [
        'cost_id',
        'created_by_id',
        'last_edited_by_id',
        'cost_type',
        'parent_id',
        'created_at',
        'updated_at',
        'is_deleted'
    ];

    const DEFINITION_MODEL = \SuttonBaker\Impresario\Model\Db\Cost::class;
    const DEFINITION_COLLECTION = \SuttonBaker\Impresario\Model\Db\Cost\Collection::class;

    const ITEM_DEFINITION_MODEL = \SuttonBaker\Impresario\Model\Db\Cost\Item::class;
    const ITEM_DEFINITION_COLLECTION = \SuttonBaker\Impresario\Model\Db\Cost\Item\Collection::class;

    const ICON = 'fa fa-tags';

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
        'sage_number' => 'Sage Number',
        'value' => 'Cost Amount',
        'supplier_name' => 'Supplier Name',
        'cost_invoice_type' => 'Type'
    ];

    const ITEM_TABLE_HEADERS = [
        'description' => 'Description',
        'qty' => 'Qty',
        'unit_price' => 'Unit Price',
        'total' => 'Total',
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