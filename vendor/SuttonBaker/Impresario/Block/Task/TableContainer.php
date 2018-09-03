<?php

namespace SuttonBaker\Impresario\Block\Task;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
/**
 * Class TaskTable
 * @package SuttonBaker\Impresario\Block\Task
 */
class TableContainer
    extends \SuttonBaker\Impresario\Block\Table\Container\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    const BLOCK_PREFIX = 'task_table';
    const ID_PARAM = 'task_id';
    const COMPLETED_KEY = 'completed';
    /** @var \SuttonBaker\Impresario\Model\Db\Task\Collection $instanceCollection */
    protected $instanceCollection;

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
     * @return \SuttonBaker\Impresario\Block\Table\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {

        if(!$this->instanceCollection){
            $this->instanceCollection = $this->getTaskHelper()->getTaskCollection();
        }

        $this->instanceCollection->addOutputProcessors([
                'target_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'status' => $this->getTaskHelper()->getStatusOutputProcessor(),
                'task_type' => $this->getTaskHelper()->getTaskTypeOutputProcessor(),
                'priority' => $this->getTaskHelper()->getPriorityOutputProcessor(),
                'delete_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getDeleteBlockHtml'])
            ]);

        $this->addChildBlock(
            $tileBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\White',
                'task.tile.block'
            )->setHeading('Active <strong>Tasks</strong>')
        );


        if(count($this->instanceCollection->load())) {
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\SuttonBaker\Impresario\Block\Table\StatusLink',
                    "task.list.table",
                    'content'
                )->setStatusKey('priority')
                    ->setRowStatusClasses(TaskDefinition::getRowClasses())
                    ->setHeaders(TaskDefinition::TABLE_HEADERS)->setRecords($this->instanceCollection->load())
                    ->addEscapeExcludes(['delete_column']
                    )
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
        }else{
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\DaveBaker\Core\Block\Html\Tag',
                    "task.list.no.records",
                    'content'
                )->setTagText('No tasks have currently been created')
            );
        }


    }

    /**
     * @return \SuttonBaker\Impresario\Block\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preRender()
    {
        parent::_preRender();


    }

    /**
     * @return string
     */
    protected function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }

    /**
     * @return string
     */
    protected function getInstanceIdParam()
    {
        return self::ID_PARAM;
    }

    /**
     * @return string
     */
    protected function getEditPageIdentifier()
    {
        return PageDefinition::TASK_EDIT;
    }
}