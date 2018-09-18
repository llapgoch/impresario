<?php

namespace SuttonBaker\Impresario\Definition;

use DaveBaker\Core\Definitions\Table;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Definition
 */
class Quote
{
    const API_ENDPOINT_UPDATE_TABLE = 'quote/updatetable';
    const API_ENDPOINT_DELETE = 'quote/delete';

    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Quote';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Quote\Collection';

    const STATUS_OPEN = 'open';
    const STATUS_WON = 'won';
    const STATUS_CLOSED_OUT = 'closed_out';
    const STATUS_CANCELLED = 'cancelled';

    const RECORDS_PER_PAGE = 20;

    const TABLE_HEADERS = [
        'quote_id' => 'ID',
        'client_name' => 'Client',
        'client_reference' => 'Client Ref',
        'status' => 'Status',
        'date_received' => 'Received',
        'created_by_name' => 'Creator',
        'project_name' => 'Project',
        'project_manager_name' => 'Project Manager',
        'estimator_name' => 'Estimator'
    ];

    const SORTABLE_COLUMNS = [
        'quote_id' => [],
        'client_name' => [Table::HEADER_SORTABLE_ALPHA],
        'client_ref' => [Table::HEADER_SORTABLE_ALPHA],
        'status' => [],
        'date_received' => [],
        'created_by_name' => [Table::HEADER_SORTABLE_ALPHA],
        'project_name' => [Table::HEADER_SORTABLE_ALPHA],
        'project_manager_name' => [Table::HEADER_SORTABLE_ALPHA],
        'estimator_name' => [Table::HEADER_SORTABLE_ALPHA]
    ];

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_WON => "Won",
            self::STATUS_CLOSED_OUT => 'Lost',
            self::STATUS_CANCELLED => "Cancelled"
        ];
    }

    /**
     * @return array
     */
    public static function getRowClasses()
    {
        return [
            self::STATUS_OPEN => 'danger',
            self::STATUS_CLOSED_OUT => 'warning',
            self::STATUS_CANCELLED => 'warning',
            self::STATUS_WON => 'success'
        ];
    }


}