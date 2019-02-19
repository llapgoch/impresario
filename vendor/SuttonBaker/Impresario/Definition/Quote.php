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
    const API_ENDPOINT_UPDATE_REVISIONS_TABLE = 'quote/updaterevisiontable';
    const API_ENDPOINT_RECORD_MONITOR = 'quote/recordmonitor';
    const API_ENDPOINT_VALIDATE_SAVE = 'quote/validatesave';
    const API_ENDPOINT_SAVE = 'quote/save';
    const API_ENDPOINT_CREATE_REVISION = 'quote/createrevision';

    const ICON = 'fa fa-calculator';

    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Quote';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Quote\Collection';

    const TENDER_STATUS_OPEN = 'open';
    const TENDER_STATUS_WON = 'won';
    const TENDER_STATUS_CLOSED_OUT = 'closed_out';
    const TENDER_STATUS_CANCELLED = 'cancelled';

    const STATUS_OPEN = 'open';
    const STATUS_IN_QUERY = 'in_query';
    const STATUS_QUOTED = 'quoted';

    const RECORDS_PER_PAGE = 20;
    const RECORDS_PER_PAGE_INLINE = 5;

    const STATUS_COLUMN = 'tender_status';
    const AGGREGATE_STATUS_COLUMN = 'aggregate_status';

    const TABLE_HEADERS = [
        'quote_id' => 'ID',
        'client_name' => 'Client',
        'site_name' => 'Site Name',
        'project_name' => 'Project',
        'date_required' => 'Required By',
        'created_by_name' => 'Creator',
        'estimator_name' => 'Estimator',
        'status' => 'Status',
        'tender_status' => 'Tender Status'
    ];

    const TABLE_HEADERS_INLINE = [
        'revision_number' => 'Revision',
        'created_at' => 'Date Created',
        'created_by_name' => 'Creator',
        'net_cost' => 'Net Cost',
        'net_sell' => 'Net Sell'
    ];

    const SORTABLE_COLUMNS = [
        'quote_id' => [],
        'client_name' => [Table::HEADER_SORTABLE_ALPHA],
        'site_name' => [Table::HEADER_SORTABLE_ALPHA],
        'status' => [],
        'tender_status' => [],
        'date_required' => [],
        'created_by_name' => [Table::HEADER_SORTABLE_ALPHA],
        'project_name' => [Table::HEADER_SORTABLE_ALPHA],
        'estimator_name' => [Table::HEADER_SORTABLE_ALPHA],
        'revision_number' => [Table::HEADER_SORTABLE_ALPHA]
    ];

    const REPORT_HEADERS = [
        'quote_id' => 'ID',
        'site_name' => 'Site',
        'project_name' => 'Project',
        'client_name' => 'Client',
        'client_reference' => 'Client Reference',
        'client_requested_by' => 'Client Requested By',
        'po_number' => 'PO Number',
        'estimator_name' => 'Estimator',
        'revision_number' => 'Revison',
        'net_cost' => 'Net Cost',
        'target_date' => 'Net Sell',
        'profit' => 'Profit',
        'gp' => 'GP',
        'status' => 'Quote Status',
        'date_returned' => 'Date Returned',
        'comments' => 'Comments',
        'date_completed' => 'Completion Date',
        'completed_by' => 'Completed By',
        'tender_status' => 'Tender Status'
    ];

    const NON_USER_VALUES = [
        'quote_id',
        'client_id',
        'client_reference',
        'site_name',
        'created_by_id',
        'last_edited_by_id',
        'enquiry_id',
        'parent_id',
        'created_at',
        'updated_at',
        'is_deleted'
    ];
    /**
     * @return array
     */
    public static function getTenderStatuses()
    {
        return [
            self::TENDER_STATUS_OPEN => 'Open',
            self::TENDER_STATUS_WON => "Won",
            self::TENDER_STATUS_CLOSED_OUT => 'Lost',
            self::TENDER_STATUS_CANCELLED => "Cancelled"
        ];
    }

    /**
     * @return array
     */
    public static function getStatuses()
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_IN_QUERY => 'In Query',
            self::STATUS_QUOTED => "Quoted"
        ];
    }


    /**
     * @return array
     */
    public static function getRowClasses()
    {
        return [
            self::TENDER_STATUS_OPEN => 'danger',
            self::TENDER_STATUS_WON => 'success',
            self::TENDER_STATUS_CLOSED_OUT => 'bg-secondary',
            self::TENDER_STATUS_CANCELLED => 'bg-dark'
        ];
    }

    public static function getAggregateStatusRowClasses()
    {
        return [
            self::STATUS_QUOTED . ":" . self::TENDER_STATUS_OPEN => 'bg-blue'
        ];
    }

    public static function getRowClass($record)
    {
        $aggregates = self::getAggregateStatusRowClasses();
        $rowClasses = self::getRowClasses();

        if(isset($aggregates[$record->getData(self::AGGREGATE_STATUS_COLUMN)])){
            return $aggregates[$record->getData(self::AGGREGATE_STATUS_COLUMN)];
        }

        if(isset($rowClasses[$record->getData(self::STATUS_COLUMN)])){
            return $rowClasses[$record->getData(self::STATUS_COLUMN)];
        }

        return '';
    }

    /**
     * @return array
     */
    public static function getInlineRowClasses()
    {
        return [
            self::TENDER_STATUS_WON => 'success'
        ];
    }

}