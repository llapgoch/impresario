<?php

namespace SuttonBaker\Impresario\Block\Task;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
/**
 * Class TaskTable
 * @package SuttonBaker\Impresario\Block\Task
 */
class TaskTable
    extends \SuttonBaker\Impresario\Block\Table\Base
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
     * @return \SuttonBaker\Impresario\Block\ListBase|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        if(!$this->instanceCollection){
            $this->instanceCollection = $this->getTaskHelper()->getTaskCollection();
        }

        $this->instanceCollection->addOutputProcessors([
                'target_date' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'status' => $this->getTaskHelper()->getStatusOutputProcessor(),
                'task_type' => $this->getTaskHelper()->getTaskTypeOutputProcessor(),
                'priority' => $this->getTaskHelper()->getPriorityOutputProcessor(),
                'edit_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getEditLinkHtml']),
                'delete_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getDeleteBlockHtml'])
            ]);

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
        $this->addChildBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Table',
                "task.list.table"
            )->setHeaders(TaskDefinition::TABLE_HEADERS)->setRecords($this->instanceCollection->load())->addEscapeExcludes(
                ['edit_column', 'delete_column']
            )
        );
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