<?php

namespace SuttonBaker\Impresario\Definition;
use DaveBaker\Core\Definitions\Table;

/**
 * Class Client
 * @package SuttonBaker\Impresario\Definition
 */
class Client
{
    const API_ENDPOINT_UPDATE_TABLE = 'client/updatetable';

    const TABLE_HEADERS = [
        'client_id' => 'ID',
        'client_name' => 'Name',
        'sales_contact_phone' => 'Sales Number',
        'sales_contact' => 'Sales Contact',
        'accounts_contact_phone' => 'Accounts Number',
        'accounts_contact' => 'Accounts Contact',
        'delete_column' => ""
    ];

    const SORTABLE_COLUMNS = [
        'client_id' => [],
        'client_name' => [Table::HEADER_SORTABLE_ALPHA],
        'sales_contact' => [],
        'accounts_contact' => []
    ];
}