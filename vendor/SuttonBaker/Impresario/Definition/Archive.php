<?php

namespace SuttonBaker\Impresario\Definition;

use DaveBaker\Core\Definitions\Table;

class Archive
{
    const ICON = 'fa fa-archive';
    const API_ENDPOINT_UPDATE_TABLE = 'archive/updatetable';

    const TABLE_HEADERS = [
        'project_id' => 'ID',
        'client_name' => 'Client',
        'site_name' => 'Site',
        'date_received' => 'Received',
        'created_by_name' => 'Creator',
        'project_name' => 'Project',
        'project_manager_name' => 'Contracts Manager',
        'net_sell' => 'Net Sell',
    ];

    const SORTABLE_COLUMNS = [
        'project_id' => [],
        'client_name' => [Table::HEADER_SORTABLE_ALPHA],
        'date_received' => [],
        'created_by_name' => [Table::HEADER_SORTABLE_ALPHA],
        'project_name' => [Table::HEADER_SORTABLE_ALPHA],
        'project_manager_name' => [Table::HEADER_SORTABLE_ALPHA],
        'site_name' => [Table::HEADER_SORTABLE_ALPHA],
        'net_sell' => [Table::HEADER_SORTABLE_NUMERIC]
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
}