<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Definition
 */
class Enquiry
{
    const STATUS_OPEN = 'open';
    const STATUS_ENGINEER_ASSIGNED = 'engineer_assigned';
    const STATUS_REPORT_COMPLETE  = 'report_complete';
    const STATUS_COMPLETE = 'complete';

    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Enquiry';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Enquiry\Collection';

    const TABLE_HEADERS = [
        'enquiry_id' => 'ID',
        'client_reference' => 'Client Reference',
        'status' => 'Status',
        'site_name' => 'Site Name',
        'date_received' => 'Date Received',
        'target_date' => 'Target Date',
        'project_manager_name' => 'Project Manager',
        'engineer_name' => 'Engineer',
        'edit_column' => '',
        'delete_column' => ''
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
            self::STATUS_COMPLETE => 'Complete'
        ];
    }
}