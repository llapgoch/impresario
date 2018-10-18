<?php

namespace SuttonBaker\Impresario\Definition;
use DaveBaker\Core\Definitions\Table;

/**
 * Class Project
 * @package SuttonBaker\Impresario\Definition
 */
class Project
{
    const API_ENDPOINT_UPDATE_TABLE = 'project/updatetable';
    const API_ENDPOINT_DELETE = 'project/delete';
    const API_ENDPOINT_VALIDATE_SAVE = 'project/validatesave';
    const API_ENDPOINT_SAVE = 'project/save';

    const ICON = 'fa fa-ravelry';

    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Project';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Project\Collection';

    const STATUS_OPEN = 'open';
    const STATUS_ON_SITE = 'onsite';
    const STATUS_COMPLETE = 'complete';
    const STATUS_CANCELLED = 'cancelled';

    const RECORDS_PER_PAGE = 20;

    const TABLE_HEADERS = [
        'project_id' => 'ID',
        'client_name' => 'Client',
        'client_reference' => 'Client Ref',
        'site_name' => 'Site',
        'project_name' => 'Project',
        'date_received' => 'Received',
        'created_by_name' => 'Creator',
        'invoice_amount_remaining' => "Amount Remaining",
        'project_manager_name' => 'Project Manager',
        'foreman_name' => 'Foreman',
        'status' => 'Status'
    ];

    const SORTABLE_COLUMNS = [
        'project_id' => [],
        'client_name' => [Table::HEADER_SORTABLE_ALPHA],
        'client_reference' => [Table::HEADER_SORTABLE_ALPHA],
        'status' => [],
        'date_received' => [],
        'created_by_name' => [Table::HEADER_SORTABLE_ALPHA],
        'project_name' => [Table::HEADER_SORTABLE_ALPHA],
        'project_manager_name' => [Table::HEADER_SORTABLE_ALPHA],
        'foreman_name' => [Table::HEADER_SORTABLE_ALPHA]
    ];

    const NON_USER_VALUES = [
        'project_id',
        'client_id',
        'client_requested_by',
        'client_reference',
        'project_name',
        'created_by_id',
        'last_edited_by_id',
        'net_cost',
        'net_sell',
        'client_id',
        'quote_id',
        'created_at',
        'updated_at',
        'is_deleted'
    ];

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'Pre-start',
            self::STATUS_ON_SITE => 'On Site',
            self::STATUS_COMPLETE=> "Complete",
            self::STATUS_CANCELLED => 'Cancelled'
        ];
    }

    /**
     * @return array
     */
    public static function getRowClasses()
    {
        return [
            self::STATUS_OPEN => 'info',
            self::STATUS_ON_SITE => 'warning',
            self::STATUS_COMPLETE => 'success',
            self::STATUS_CANCELLED => 'bg-dark'
        ];
    }


}