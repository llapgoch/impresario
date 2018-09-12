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
    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Project';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Project\Collection';

    const STATUS_OPEN = 'open';
    const STATUS_COMPLETE = 'complete';
    const STATUS_CANCELLED = 'cancelled';

    const RECORDS_PER_PAGE = 20;

    const TABLE_HEADERS = [
        'project_id' => 'ID',
        'client_name' => 'Client',
        'client_reference' => 'Client Ref',
        'status' => 'Status',
        'date_received' => 'Received',
        'created_by_name' => 'Creator',
        'project_name' => 'Project',
        'project_manager_name' => 'Project Manager',
        'foreman_name' => 'Foreman'
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

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'Open',
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
            self::STATUS_OPEN => 'danger',
            self::STATUS_COMPLETE => 'success',
            self::STATUS_CANCELLED => 'warning'
        ];
    }


}