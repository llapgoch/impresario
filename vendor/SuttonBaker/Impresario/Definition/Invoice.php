<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Invoice
 * @package SuttonBaker\Impresario\Definition
 */
class Invoice
{
    const API_ENDPOINT_UPDATE_TABLE = 'invoice/updatetable';
    const API_ENDPOINT_DELETE = 'invoice/delete';
    const API_ENDPOINT_RECORD_MONITOR = 'invoice/recordmonitor';
    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Invoice';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Invoice\Collection';
    const ICON = 'fa fa-gbp';

    const INVOICE_TYPE_ENQUIRY = 'enquiry';
    const INVOICE_TYPE_PROJECT = 'project';

    const RECORDS_PER_PAGE = 20;

    const TABLE_HEADERS = [
        'invoice_id' => 'ID',
        'invoice_date' => 'Date',
        'invoice_number' => 'Number',
        'value' => 'Invoice Amount'
    ];

    /**
     * @return array
     */
    public static function getInvoiceTypes()
    {
        return [
            self::INVOICE_TYPE_ENQUIRY => 'Enquiry',
            self::INVOICE_TYPE_PROJECT => 'Project'
        ];
    }

}