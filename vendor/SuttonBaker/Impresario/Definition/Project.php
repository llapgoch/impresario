<?php

namespace SuttonBaker\Impresario\Definition;
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