<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Invoice
 * @package SuttonBaker\Impresario\Definition
 */
class Invoice
{
    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Invoice';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Invoice\Collection';

    const INVOICE_TYPE_ENQUIRY = 'enquiry';
    const INVOICE_TYPE_PROJECT = 'project';

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

    /**
     * @param string $invoiceType
     * @return string
     */
    public static function getInvoiceTypeLabel($invoiceType)
    {
        $invoiceTypes = self::getInvoiceTypes();

        if(in_array($invoiceType, array_keys($invoiceType))){
            return $invoiceTypes[$invoiceType];
        }

        return '';
    }

}