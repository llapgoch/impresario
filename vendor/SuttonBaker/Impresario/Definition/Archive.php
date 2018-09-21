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
        'date_received' => 'Received',
        'created_by_name' => 'Creator',
        'total_net_cost' => 'Net Cost',
        'total_net_sell' => 'Net Sell',
        'profit' => 'Profit',
        'project_name' => 'Project',
        'project_manager_name' => 'Project Manager',
        'foreman_name' => 'Foreman',
    ];

    const SORTABLE_COLUMNS = [
        'project_id' => [],
        'client_name' => [Table::HEADER_SORTABLE_ALPHA],
        'date_received' => [],
        'created_by_name' => [Table::HEADER_SORTABLE_ALPHA],
        'total_net_cost' => [],
        'total_net_sell' => [],
        'profit' => [],
        'project_name' => [Table::HEADER_SORTABLE_ALPHA],
        'project_manager_name' => [Table::HEADER_SORTABLE_ALPHA],
        'foreman_name' => [Table::HEADER_SORTABLE_ALPHA]
    ];
}