<?php

namespace SuttonBaker\Impresario\Api;

use DaveBaker\Core\Api\Exception;
use DaveBaker\Core\Block\Components\Paginator;
use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Block\Table\StatusLink;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Project
 * @package SuttonBaker\Impresario\Api
 *
 */
class Archive
extends \DaveBaker\Core\Api\Base
{
    /** @var string  */
    protected $blockPrefix = 'archive';
    /** @var array  */
    protected $capabilities = [Roles::CAP_VIEW_PROJECT];

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function updatetableAction($params, \WP_REST_Request $request)
    {
        $blockManager = $this->getApp()->getBlockManager();

        /** @var StatusLink $tableBlock */
        $tableBlock = $blockManager->getBlock("project.list.table");
        /** @var \SuttonBaker\Impresario\Block\Quote\QuoteList $list */
        $list = $blockManager->getBlock("project.list");
        
        if (isset($params['order']['dir']) && isset($params['order']['column'])) {
            $tableBlock->setColumnOrder($params['order']['column'], $params['order']['dir']);
        }

        /** @var Paginator $paginatorBlock */
        $paginatorBlock = $blockManager->getBlock("project.list.paginator");

        if (isset($params['customData']['filters'])) {
            $tableBlock->setFilters(
                json_decode($params['customData']['filters'], true)
            );
        }

        // Required at this point to get the recordset after the filters have been applied
        $list->applyRecordCountToPaginator();


        if (isset($params['pageNumber'])) {
            $paginatorBlock->setPage($params['pageNumber']);
        }

        $list->render();
        $noItems = $blockManager->getBlock("project.list.table.noitems");

        $this->addReplacerBlock([$tableBlock, $paginatorBlock, $noItems]);
    }
}
