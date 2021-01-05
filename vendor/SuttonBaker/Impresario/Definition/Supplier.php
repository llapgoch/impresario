<?php

namespace SuttonBaker\Impresario\Definition;
use DaveBaker\Core\Definitions\Table;

/**
 * Class Supplier
 * @package SuttonBaker\Impresario\Definition
 */
class Supplier
{
    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Supplier';
    const API_ENDPOINT_UPDATE_TABLE = 'supplier/updatetable';
    const API_ENDPOINT_RECORD_MONITOR = 'supplier/recordmonitor';
    const API_ENDPOINT_DELETE = 'supplier/delete';
    const ICON = 'fa fa-address-card-o';

    const RECORDS_PER_PAGE = 20;

    const TABLE_HEADERS = [
        'supplier_id' => 'ID',
        'supplier_name' => 'Name',
        'supplier_contact_phone' => 'Supplier Number',
        'supplier_contact' => 'Supplier Contact',
        'accounts_contact_phone' => 'Accounts Number',
        'accounts_contact' => 'Accounts Contact'
    ];

    const SORTABLE_COLUMNS = [
        'supplier_id' => [],
        'supplier_name' => [Table::HEADER_SORTABLE_ALPHA],
        'supplier_contact' => [],
        'accounts_contact' => []
    ];
}