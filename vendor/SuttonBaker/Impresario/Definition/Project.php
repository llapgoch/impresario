<?php

namespace SuttonBaker\Impresario\Definition;

use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Definition\Filter as FilterDefinition;

/**
 * Class Project
 * @package SuttonBaker\Impresario\Definition
 */
class Project
{
    const API_ENDPOINT_UPDATE_TABLE = 'project/updatetable';
    const API_ENDPOINT_DELETE = 'project/delete';
    const API_ENDPOINT_VALIDATE_SAVE = 'project/validatesave';
    const API_ENDPOINT_RECORD_MONITOR = 'project/recordmonitor';
    const API_ENDPOINT_SAVE = 'project/save';

    const ICON = 'fa fa-ravelry';

    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Project';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Project\Collection';

    const STATUS_OPEN = 'open';
    const STATUS_ON_SITE = 'onsite';
    const STATUS_COMPLETE = 'complete';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ON_HOLD = 'on_hold';
    const STATUS_ON_HOLD_VRF_SUBMITTED = 'on_hold_vrf_sub';
    const STATUS_ON_HOLD_VRF_REQUIRED = 'on_hold_vrd_req';

    const RECORDS_PER_PAGE = 20;

    const FILTER_LISTING = [
        'client_id' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_EQ,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{project}}.client_id"
        ],
        'client_reference' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_LIKE,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{project}}.client_reference"
        ],
        'site_name' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_LIKE,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{project}}.site_name"
        ],
        'project_name' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_LIKE,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{project}}.project_name"
        ],
        'date_received' => [
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_RANGE,
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_RANGE,
            FilterDefinition::MAP => "{{projcet}}.date_received",
            FilterDefinition::DATA_CONVERTER => [
                FilterDefinition::DATA_CONVERTER_CLASS => \DaveBaker\Core\Helper\Date::class,
                FilterDefinition::DATA_CONVERTER_METHOD => 'localDateToDb'
            ]
        ],
        'project_manager_id' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_EQ,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{project}}.project_manager_id"
        ],
        'assigned_foreman_id' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_EQ,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{project}}.assigned_foreman_id"
        ],
        'status' => [
            FilterDefinition::COMPARE_TYPE => FilterDefinition::COMPARE_TYPE_EQ,
            FilterDefinition::FIELD_TYPE => FilterDefinition::FIELD_TYPE_TEXT,
            FilterDefinition::MAP => "{{project}}.status"
        ]
    ];

    const TABLE_HEADERS = [
        'project_id' => 'ID',
        'client_name' => 'Client',
        'client_reference' => 'Client Ref',
        'site_name' => 'Site',
        'project_name' => 'Project',
        'date_received' => 'Received',
        'invoice_amount_remaining' => "Amount Remaining",
        'project_manager_name' => 'Contracts Manager',
        'foreman_name' => 'Foreman',
        'status' => 'Status'
    ];

    const SORTABLE_COLUMNS = [
        'project_id' => [],
        'client_name' => [Table::HEADER_SORTABLE_ALPHA],
        'client_reference' => [Table::HEADER_SORTABLE_ALPHA],
        'status' => [],
        'date_received' => [],
        'project_name' => [Table::HEADER_SORTABLE_ALPHA],
        'site_name' => [Table::HEADER_SORTABLE_ALPHA],
        'project_manager_name' => [Table::HEADER_SORTABLE_ALPHA],
        'foreman_name' => [Table::HEADER_SORTABLE_ALPHA]
    ];

    const REPORT_HEADERS = [
        'project_id' => 'ID',
        'site_name' => 'Site',
        'project_name' => 'Project',
        'client_name' => 'Client',
        'client_reference' => 'Client Reference',
        'po_number' => 'PO Number',
        'project_start_date' => 'Start Date',
        'project_end_date' => 'End Date',
        'project_manager_name' => 'Contracts Manager',
        'foreman_name' => 'Foreman',
        'client_project_manager' => 'Client Project Manager',
        'net_cost' => 'Net Cost',
        'net_sell' => 'Net Sell',
        'profit' => 'Profit',
        'amount_invoiced' => 'Amount Invoiced',
        'invoice_amount_remaining' => 'Invoice Amount Remaining',
        'actual_cost' => 'Actual Cost',
        'actual_profit' => 'Actual Profit',
        'actual_margin' => 'Actual Margin',
        'notes' => 'Notes',
        'status' => 'Status'
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
            self::STATUS_ON_HOLD => 'On Hold',
            self::STATUS_ON_HOLD_VRF_REQUIRED => 'On Hold - VRF Required',
            self::STATUS_ON_HOLD_VRF_SUBMITTED => 'On Hold - VRF Submitted',
            self::STATUS_COMPLETE => "Complete",
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
