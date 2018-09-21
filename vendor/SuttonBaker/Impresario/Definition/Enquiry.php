<?php

namespace SuttonBaker\Impresario\Definition;
use DaveBaker\Core\Definitions\Table;

/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Definition
 */
class Enquiry
{
    const API_ENDPOINT_UPDATE_TABLE = 'enquiry/updatetable';
    const API_ENDPOINT_DELETE = 'enquiry/delete';
    const API_ENDPOINT_SAVE_VALIDATOR = 'enquiry/savevalidator';

    const ICON = 'fa fa-thumb-tack';

    const STATUS_OPEN = 'open';
    const STATUS_ENGINEER_ASSIGNED = 'engineer_assigned';
    const STATUS_REPORT_COMPLETE  = 'report_complete';
    const STATUS_COMPLETE = 'complete';
    const STATUS_INVOICED = 'invoiced';
    const STATUS_CANCELLED = 'cancelled';

    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Enquiry';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Enquiry\Collection';

    const RECORDS_PER_PAGE = 20;

    const TABLE_HEADERS = [
        'enquiry_id' => 'ID',
        'client_reference' => 'Client Ref',
        'status' => 'Status',
        'site_name' => 'Site',
        'date_received' => 'Received',
        'target_date' => 'Target',
        'assigned_to_name' => 'Assignee',
        'engineer_name' => 'Engineer'
    ];

    const SORTABLE_COLUMNS = [
        'enquiry_id' => [],
        'client_reference' => [Table::HEADER_SORTABLE_ALPHA],
        'status' => [],
        'site_name' => [Table::HEADER_SORTABLE_ALPHA],
        'date_received' => [],
        'target_date' => [],
        'assigned_to_name' => [Table::HEADER_SORTABLE_ALPHA],
        'engineer_name' => [Table::HEADER_SORTABLE_ALPHA]
    ];

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_ENGINEER_ASSIGNED => 'Engineer Assigned',
            self::STATUS_REPORT_COMPLETE => 'Report Complete',
            self::STATUS_INVOICED => 'Invoiced',
            self::STATUS_COMPLETE => 'Complete',
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
            self::STATUS_ENGINEER_ASSIGNED => 'warning',
            self::STATUS_REPORT_COMPLETE => 'warning',
            self::STATUS_INVOICED => 'bg-blue',
            self::STATUS_COMPLETE => 'success'
        ];
    }
}