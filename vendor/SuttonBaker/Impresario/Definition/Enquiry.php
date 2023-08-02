<?php

namespace SuttonBaker\Impresario\Definition;
use \DaveBaker\Core\Definitions\Table;
use \SuttonBaker\Impresario\Definition\Filter as FilterDefinition;

/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Definition
 */
class Enquiry
{
    const API_ENDPOINT_UPDATE_TABLE = 'enquiry/updatetable';
    const API_ENDPOINT_DELETE = 'enquiry/delete';
    const API_ENDPOINT_VALIDATE_SAVE = 'enquiry/validatesave';
    const API_ENDPOINT_RECORD_MONITOR = 'enquiry/recordmonitor';
    const API_ENDPOINT_SAVE = 'enquiry/save';

    const ICON = 'fa fa-thumb-tack';

    const STATUS_OPEN = 'open';
    const STATUS_ENGINEER_ASSIGNED = 'engineer_assigned';
    const STATUS_READY_TO_INVOICE = 'ready_to_invoice';
    const STATUS_COMPLETE = 'complete';
    const STATUS_INVOICED = 'invoiced';
    const STATUS_CANCELLED = 'cancelled';

    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Enquiry';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Enquiry\Collection';

    const RECORDS_PER_PAGE = 20;

    const FILTER_LISTING = [
        'enquiry_id' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_EQ,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{enquiry}}.enquiry_id"
        ],
        'status' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_EQ,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{enquiry}}.status"
        ],
        'priority' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_EQ,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{enquiry}}.priority"
        ],
        'client_id' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_EQ,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{enquiry}}.client_id"
        ],
        'client_reference' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_LIKE,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{enquiry}}.client_reference"
        ],
        'date_received' => [
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_RANGE,
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_RANGE,
            FilterDefinition::MAP => "{{enquiry}}.date_received",
            FilterDefinition::DATA_CONVERTER => [
                FilterDefinition::DATA_CONVERTER_CLASS => \DaveBaker\Core\Helper\Date::class,
                FilterDefinition::DATA_CONVERTER_METHOD => 'localDateToDb'
            ]
        ],
        'assigned_to_id' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_EQ,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{enquiry}}.assigned_to_id"
        ],
        'mi_number' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_LIKE,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{enquiry}}.mi_number"
        ],
        'site_name' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_LIKE,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{enquiry}}.site_name"
        ]
    ];

    const TABLE_HEADERS = [
        'enquiry_id' => 'ID',
        'client_name' => 'Client',
        'client_reference' => 'Client Ref',
        'status' => 'Status',
        'site_name' => 'Site',
        'priority' => 'Priority',
        'date_received' => 'Received',
        'target_date' => 'Target',
        'assigned_to_name' => 'Assignee',
        'mi_number' => 'MI Number'
    ];

    const SORTABLE_COLUMNS = [
        'enquiry_id' => [],
        'client_reference' => [Table::HEADER_SORTABLE_ALPHA],
        'client_name' => [Table::HEADER_SORTABLE_ALPHA],
        'status' => [],
        'priority' => [],
        'site_name' => [Table::HEADER_SORTABLE_ALPHA],
        'date_received' => [],
        'target_date' => [],
        'assigned_to_name' => [Table::HEADER_SORTABLE_ALPHA],
        'mi_number' => [Table::HEADER_SORTABLE_ALPHA]
    ];

    const REPORT_HEADERS = [
        'enquiry_id' => 'ID',
        'site_name' => 'Site',
        'priority' => 'Priority',
        'client_name' => 'Client',
        'client_reference' => 'Client Ref',
        'client_requested_by' => 'Client Requested By',
        'po_number' => 'PO Number',
        'assigned_to_name' => 'Assignee',
        'engineer_name' => 'Engineer',
        'notes' => 'Notes',
        'date_received' => 'Received',
        'target_date' => 'Target',
        'status' => 'Enquiry Status',
        'date_completed' => 'Date Completed'
    ];


    /** @var array  */
    const NON_USER_VALUES = [
        'enquiry_id',
        'created_by_id',
        'created_at',
        'updated_at',
        'is_deleted',
        'last_edited_by_id'
    ];

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_ENGINEER_ASSIGNED => 'Engineer Assigned',
            self::STATUS_READY_TO_INVOICE => 'Ready To Invoice',
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
            self::STATUS_READY_TO_INVOICE => 'success',
            self::STATUS_INVOICED => 'bg-blue',
            self::STATUS_COMPLETE => 'success'
        ];
    }
}