<?php

namespace SuttonBaker\Impresario\Api;
use DaveBaker\Core\Block\Components\Paginator;
use SuttonBaker\Impresario\Block\Table\StatusLink;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Project
 * @package SuttonBaker\Impresario\Api
 *
 */
class Project
    extends \DaveBaker\Core\Api\Base
{
    /** @var string  */
    protected $blockPrefix = 'quote';
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
        $tableBlock = $blockManager->getBlock("{$this->blockPrefix}.list.table");

        if(isset($params['order']['dir']) && isset($params['order']['column'])){
            $tableBlock->setColumnOrder($params['order']['column'], $params['order']['dir']);
        }

        /** @var Paginator $paginatorBlock */
        $paginatorBlock = $blockManager->getBlock("{$this->blockPrefix}.list.paginator");

        if(isset($params['pageNumber'])){
            $paginatorBlock->setPage($params['pageNumber']);
        }

        $this->addReplacerBlock([$tableBlock, $paginatorBlock]);
    }

}