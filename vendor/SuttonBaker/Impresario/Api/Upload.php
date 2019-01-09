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
        $blockManager = $this->getApp()->getBlockManager();
        $block = $blockManager->createBlock(
            \SuttonBaker\Impresario\Block\Upload\TableContainer::class,
            'file.upload.container'
        );

        $block->preDispatch();

        var_dump($block->render());

exit;

        $taskHelper = $this->createAppObject('\SuttonBaker\Impresario\Helper\Task');

        /** @var StatusLink $tableBlock */
        $taskTable = $blockManager->getBlock("{$this->blockPrefix}.list.table");
        $taskType = isset($params['type']) ? $params['type'] : null;
        $parentId = isset($params['parent_id']) ? $params['parent_id'] : null;

           // Gah, has to add a task specific logic to get tasks for grouped quotes
        if($taskType == TaskDefinition::TASK_TYPE_QUOTE){
            $instanceCollection = $this->getQuoteHelper()->getTasksForQuote($params['parent_id']);
            $taskTable->setRecords($instanceCollection);
        }else{
            $instanceCollection = $taskTable->getCollection();

            if($parentId){
                $instanceCollection->where('parent_id=?', $params['parent_id']);
            }

            if($taskType){
                $instanceCollection->where('task_type=?', $taskType);
            }
        }

        $this->getTaskHelper()->addOutputProcessorsToCollection($instanceCollection);

        if(isset($params['order']['dir']) && isset($params['order']['column'])){
            $taskTable->setColumnOrder($params['order']['column'], $params['order']['dir']);
        }

        /** @var Paginator $paginatorBlock */
        $paginatorBlock = $blockManager->getBlock("{$this->blockPrefix}.list.paginator");

        // For inline task blocks
        if (isset($params['type']) && isset($params['parent_id'])) {

            $taskTable->removeHeader(['task_id', 'task_type'])
                ->addJsDataItems([
                    Table::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                        $this->getUrlHelper()->getApiUrl(
                            TaskDefinition::API_ENDPOINT_UPDATE_TABLE,
                            [
                                'type' => $params['type'],
                                'parent_id' => $params['parent_id']
                            ]

                        )
                ]);

            $paginatorBlock->setRecordsPerPage(TaskDefinition::RECORDS_PER_PAGE_INLINE)
                ->removeClass('pagination-xl')->addClass('pagination-xs');
        }
        
        if(isset($params['pageNumber'])){
            $paginatorBlock->setPage($params['pageNumber']);
        }

        $this->addReplacerBlock([$taskTable, $paginatorBlock]);
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