<?php

namespace SuttonBaker\Impresario\Api;
use DaveBaker\Core\Block\Components\Paginator;
use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Block\Table\StatusLink;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Definition\Task as TaskDefinition;

/**
 * Class Task
 * @package SuttonBaker\Impresario\Api
 *
 */
class Task
    extends \DaveBaker\Core\Api\Base
{
    /** @var string  */
    protected $blockPrefix = 'task.table';
    /** @var array  */
    protected $capabilities = [Roles::CAP_VIEW_TASK];

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
        $taskHelper = $this->createAppObject('\SuttonBaker\Impresario\Helper\Task');

        /** @var StatusLink $tableBlock */
        $tableBlock = $blockManager->getBlock("{$this->blockPrefix}.list.table");

        if(isset($params['order']['dir']) && isset($params['order']['column'])){
            $tableBlock->setColumnOrder($params['order']['column'], $params['order']['dir']);
        }

        /** @var '\SuttonBaker\Impresario\Block\Table\StatusLink' $taskTable */
        $taskTable = $blockManager->getBlock("{$this->blockPrefix}.list.table");
        /** @var \SuttonBaker\Impresario\Model\Db\Task\Collection $instanceCollection */

        /** @var Paginator $paginatorBlock */
        $paginatorBlock = $blockManager->getBlock("{$this->blockPrefix}.list.paginator");

        if($instanceCollection = $taskTable->getCollection()) {
            // For inline task blocks
            if (isset($params['type']) && isset($params['parent_id'])) {

                $tableBlock->removeHeader(['status', 'task_id', 'task_type'])
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

                $instanceCollection->where('task_type=?', $params['type'])
                    ->where('parent_id=?', $params['parent_id']);

                $paginatorBlock->setRecordsPerPage(TaskDefinition::RECORDS_PER_PAGE_INLINE)
                    ->removeClass('pagination-xl')->addClass('pagination-xs');
            }
        }

        if(isset($params['pageNumber'])){
            $paginatorBlock->setPage($params['pageNumber']);
        }

        $this->addReplacerBlock([$tableBlock, $paginatorBlock]);
    }

}