<?php

namespace SuttonBaker\Impresario\Definition;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Definition
 */
class Quote
{
    const DEFINITION_MODEL = '\SuttonBaker\Impresario\Model\Db\Quote';
    const DEFINITION_COLLECTION = '\SuttonBaker\Impresario\Model\Db\Quote\Collection';

    const STATUS_OPEN = 'open';
    const STATUS_WON = 'won';
    const STATUS_CLOSED_OUT = 'closed_out';
    const STATUS_CANCELLED = 'cancelled';

    const TABLE_HEADERS = [
        'quote_id' => 'ID',
        'client_reference' => 'Client Reference',
        'status' => 'Status',
        'date_received' => 'Date Received',
        'created_by_name' => 'Created By',
        'client' => 'Client',
        'project_name' => 'Project Name',
        'project_manager_name' => 'Project Manager',
        'estimator_name' => 'Estimator',
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
            self::STATUS_WON => "Won",
            self::STATUS_CLOSED_OUT => 'Lost',
            self::STATUS_CANCELLED => "Cancelled"
        ];
    }


}