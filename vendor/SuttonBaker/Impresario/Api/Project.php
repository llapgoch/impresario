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
class Project
    extends \DaveBaker\Core\Api\Base
{
    /** @var string  */
    protected $blockPrefix = 'project';
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

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function deleteAction($params, \WP_REST_Request $request)
    {
        /** @var \SuttonBaker\Impresario\Helper\Project $helper */
        $helper = $this->createAppObject('\SuttonBaker\Impresario\Helper\Project');

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['id'])){
            throw new Exception('The item could not be found');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Project $item */
        $item = $this->createAppObject(
            \SuttonBaker\Impresario\Definition\Project::DEFINITION_MODEL
        )->load($params['id']);

        if(!$item->getId()){
            throw new Exception('The project could not be found');
        }

        $helper->deleteProject($item);
        $this->addMessage('The project has been removed', Messages::SUCCESS);

        return true;
    }

}