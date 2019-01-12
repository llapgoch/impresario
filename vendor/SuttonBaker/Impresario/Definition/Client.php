<?php

namespace SuttonBaker\Impresario\Definition;
use DaveBaker\Core\Definitions\Table;

/**
 * Class Client
 * @package SuttonBaker\Impresario\Definition
 */
class Client
{
    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Client';
    const API_ENDPOINT_UPDATE_TABLE = 'client/updatetable';
    const API_ENDPOINT_RECORD_MONITOR = 'client/recordmonitor';
    const API_ENDPOINT_DELETE = 'client/delete';
    const ICON = 'fa fa-address-book';

    const RECORDS_PER_PAGE = 20;

    const TABLE_HEADERS = [
        'client_id' => 'ID',
        'client_name' => 'Name',
        'sales_contact_phone' => 'Sales Number',
        'sales_contact' => 'Sales Contact',
        'accounts_contact_phone' => 'Accounts Number',
        'accounts_contact' => 'Accounts Contact'
    ];

    const SORTABLE_COLUMNS = [
        'client_id' => [],
        'client_name' => [Table::HEADER_SORTABLE_ALPHA],
        'sales_contact' => [],
        'accounts_contact' => []
    ];
}