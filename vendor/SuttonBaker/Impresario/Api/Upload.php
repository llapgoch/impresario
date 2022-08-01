<?php

namespace SuttonBaker\Impresario\Api;
use DaveBaker\Core\Api\Exception;
use DaveBaker\Core\Block\Components\Paginator;
use DaveBaker\Core\Definitions\Messages;
use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Block\Table\StatusLink;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Definition\Upload as UploadDefinition;

/**
 * Class Task
 * @package SuttonBaker\Impresario\Api
 *
 */
class Upload
    extends Base
{
    /** @var string  */
    protected $blockPrefix = 'upload';
    /** @var array  */
    protected $capabilities = [];

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function updatetableAction($params, \WP_REST_Request $request)
    {

        $uploadType = isset($params['upload_type']) ? $params['upload_type'] : null;
        $parentId = isset($params['parent_id']) ? $params['parent_id'] : null;
        // The block prefix is now configurable for multiple elements on a page - see how projects with completion certificates is done
        // Default to 'upload' so that existing uploaders don't need the new block_prefix parameter
        $blockPrefix = isset($params['block_prefix']) ? $params['block_prefix'] : $this->blockPrefix;
        $showDelete = isset($params['show_delete']) ? (bool) $params['show_delete'] : true;

        $blockManager = $this->getApp()->getBlockManager();
        $block = $blockManager->createBlock(
            \SuttonBaker\Impresario\Block\Upload\TableContainer::class,
            'file.upload.container'
        )->setIdentifier($parentId)
            ->setUploadType($uploadType)
            ->setBlockPrefix($blockPrefix)
            ->setShowDelete($showDelete);

        $block->preDispatch();

        $listBlock = $blockManager->getBlock("{$blockPrefix}.list.table");

        /** @var Paginator $paginatorBlock */
        $paginatorBlock = $blockManager->getBlock("{$blockPrefix}.list.paginator");

        if(isset($params['pageNumber'])){
            $paginatorBlock->setPage($params['pageNumber']);
        }

        $this->addReplacerBlock([$listBlock, $paginatorBlock]);
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
        /** @var \SuttonBaker\Impresario\Helper\Task $helper */
        $helper = $this->createAppObject('\SuttonBaker\Impresario\Helper\Task');

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['id'])){
            throw new Exception('The item could not be found');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Task $item */
        $item = $this->createAppObject(
            TaskDefinition::DEFINITION_MODEL
        )->load($params['id']);

        if(!$item->getId()){
            throw new Exception('The project could not be found');
        }

        $helper->deleteTask($item);
        $this->addMessage('The task has been removed', Messages::SUCCESS);

        return true;
    }

}