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
    const STATUS_QUOTED = 'quoted';

    const RECORDS_PER_PAGE = 20;
    const RECORDS_PER_PAGE_INLINE = 5;

    const TABLE_HEADERS = [
        'quote_id' => 'ID',
        'client_name' => 'Client',
        'client_reference' => 'Client Ref',
        'status' => 'Status',
        'tender_status' => 'Tender Status',
        'date_received' => 'Received',
        'created_by_name' => 'Creator',
        'project_name' => 'Project',
        'estimator_name' => 'Estimator'
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
        'client_ref' => [Table::HEADER_SORTABLE_ALPHA],
        'status' => [],
        'tender_status' => [],
        'date_received' => [],
        'created_by_name' => [Table::HEADER_SORTABLE_ALPHA],
        'project_name' => [Table::HEADER_SORTABLE_ALPHA],
        'estimator_name' => [Table::HEADER_SORTABLE_ALPHA],
        'revision_number' => [Table::HEADER_SORTABLE_ALPHA]
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