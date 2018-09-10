<?php

namespace SuttonBaker\Impresario\Api;
use \SuttonBaker\Impresario\Definition\Client as ClientDefinition;
use DaveBaker\Core\Definitions\Table as TableDefinition;

/**
 * Class Client
 * @package SuttonBaker\Impresario\Api
 *
 */
class Client
    extends \DaveBaker\Core\Api\Base
{

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function updatetableAction($params, \WP_REST_Request $request)
    {
        $tableList = $this->getApp()->getBlockManager()->createBlock(
            '\SuttonBaker\Impresario\Block\Client\ClientList',
            'client.list'
        )->preDispatch();

        $tableBlock = $this->getApp()->getBlockManager()->getBlock('client.list.table');

        if(isset($params['order']['dir']) && isset($params['order']['column'])){
            $tableBlock->setColumnOrder($params['order']['column'], $params['order']['dir']);
        }


        $this->addReplacerBlock($tableBlock);
    }

}