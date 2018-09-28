<?php

namespace SuttonBaker\Impresario\Block\Task;

use DaveBaker\Core\Definitions\Table;
use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
/**
 * Class TableContainer
 * @package SuttonBaker\Impresario\Block\Task
 */
class TableContainer
    extends \SuttonBaker\Impresario\Block\Table\Container\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    /** @var string  */
    protected $blockPrefix = 'task.table';
    /** @var string  */
    protected $idParam = 'task_id';
    /** @var \SuttonBaker\Impresario\Model\Db\Task\Collection $instanceCollection */
    protected $instanceCollection;
    /** @var string  */
    protected $tileDefinitionClass = '\SuttonBaker\Impresario\Block\Core\Tile\White';
    /** @var bool  */
    protected $showSuperseded = false;
    /** @var bool  */
    protected $showNoItemsMessage = true;
    /** @var int  */
    protected $recordsPerPage = TaskDefinition::RECORDS_PER_PAGE;

    /**
     * @return int
     */
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    /**
     * @param int $recordsPerPage
     * @return $this
     */
    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = $recordsPerPage;
        return $this;
    }

    /**
     * @return bool
     */
    public function getShowNoItemsMessage()
    {
        return $this->showNoItemsMessage;
    }

    /**
     * @param bool $showNoItemsMessage
     * @return $this
     */
    public function setShowNoItemsMessage($showNoItemsMessage)
    {
        $this->showNoItemsMessage = $showNoItemsMessage;
        return $this;
    }

    /**
     * @param bool $showSuperseded
     * @return $this
     */
    public function setShowSuperseded($showSuperseded)
    {
        $this->showSuperseded = $showSuperseded;
        return $this;
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Task\Collection
     */
    public function getInstanceCollection()
    {
        return $this->instanceCollection;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Task\Collection $instanceCollection
     * @return $this
     */
    public function setInstanceCollection(
        \SuttonBaker\Impresario\Model\Db\Task\Collection $instanceCollection
    ) {
        $this->instanceCollection = $instanceCollection;
        return $this;
    }

    /**
     * @return \SuttonBaker\Impresario\Block\Table\Container\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function _preDispatch()
    {
        wp_enqueue_script('dbwpcore_table_updater');

        if(!$this->instanceCollection){
            $this->instanceCollection = $this->getTaskHelper()->getTaskCollection();
        }

        if($this->showSuperseded == false){
            $this->instanceCollection->where('is_superseded=?', 0);
        }

        $this->instanceCollection->addOutputProcessors([
                'target_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'status' => $this->getTaskHelper()->getStatusOutputProcessor(),
                'task_type' => $this->getTaskHelper()->getTaskTypeOutputProcessor(),
                'priority' => $this->getTaskHelper()->getPriorityOutputProcessor()
            ]);

        $instanceItems = $this->instanceCollection->load();


        $this->addChildBlock(
            $tileBlock = $this->createBlock(
                $this->getTileDefinitionClass(),
                "{$this->getBlockPrefix()}.tile.block"
            )->setHeading('<strong>Task</strong> List')
        );

        $tileBlock->addChildBlock(
        /** @var Paginator $paginator */
            $paginator = $this->createBlock(
                '\DaveBaker\Core\Block\Components\Paginator',
                "{$this->getBlockPrefix()}.list.paginator",
                'footer'
            )->setRecordsPerPage($this->getRecordsPerPage())
                ->setTotalRecords(count($instanceItems))
                ->setIsReplacerBlock(true)
        );

        if(count($instanceItems)) {
            $tileBlock->setTileBodyClass('nopadding');

            /** \SuttonBaker\Impresario\Block\Table\StatusLink $tableBlock */
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\SuttonBaker\Impresario\Block\Table\StatusLink',
                    "{$this->getBlockPrefix()}.list.table",
                    'content'
                )->setTemplate('html/table/task.phtml')
                    ->setStatusKey('priority')
                    ->setRowStatusClasses(TaskDefinition::getRowClasses())
                    ->setHeaders(TaskDefinition::TABLE_HEADERS)
                    ->setRecords($this->instanceCollection)
                    ->setSortableColumns(TaskDefinition::SORTABLE_COLUMNS)
                    ->addJsDataItems([
                        Table::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                            $this->getUrlHelper()->getApiUrl(TaskDefinition::API_ENDPOINT_UPDATE_TABLE)
                    ])
                    ->setPaginator($paginator)
            );

            $tableBlock->setLinkCallback(
                function ($headerKey, $record) {
                    return $this->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::TASK_EDIT,
                        ['task_id' => $record->getId()],
                        true
                    );
                }
            );
        } elseif ($this->getShowNoItemsMessage()) {
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\DaveBaker\Core\Block\Html\Tag',
                    "{$this->getBlockPrefix()}.list.no.records",
                    'content'
                )->setTagText('No tasks have currently been created')
            );
        }
    }
}