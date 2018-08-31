<?php

namespace SuttonBaker\Impresario\Block\Task;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
/**
 * Class TaskList
 * @package SuttonBaker\Impresario\Block\Task
 */
class TaskList
    extends \SuttonBaker\Impresario\Block\ListBase
    implements \DaveBaker\Core\Block\BlockInterface
{
    const BLOCK_PREFIX = 'task';
    const COMPLETED_KEY = 'completed';
    const ID_PARAM = 'task_id';

    /**
     * @return \DaveBaker\Core\Block\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $tableHeaders = TaskDefinition::TABLE_HEADERS;

        /** @var \SuttonBaker\Impresario\Model\Db\Task\Collection $instanceCollection */
        $instanceCollection = $this->getTaskHelper()->getTaskCollection()
            ->addOutputProcessors([
                'target_date' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'status' => $this->getTaskHelper()->getStatusOutputProcessor(),
                'task_type' => $this->getTaskHelper()->getTaskTypeOutputProcessor(),
                'priority' => $this->getTaskHelper()->getPriorityOutputProcessor(),
                'edit_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getLinkHtml']),
                'delete_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getDeleteBlockHtml'])
            ]);

        if($this->getRequest()->getParam(self::COMPLETED_KEY)){
            $instanceCollection->getSelect()->where(
                'status=?',
                \SuttonBaker\Impresario\Definition\Task::STATUS_COMPLETE
            );
        }

        $this->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Component\ActionBar',
                "{$this->getBlockPrefix()}.list.action.bar"
            )->addActionItem(
                'All Tasks',
                $this->getPageUrl(\SuttonBaker\Impresario\Definition\Page::TASK_LIST)
            )->addActionItem(
                'Completed Tasks',
                $this->getPageUrl(\SuttonBaker\Impresario\Definition\Page::TASK_LIST, ['completed' => 1])
            )
        );

        $this->addChildBlock(
            $this->getMessagesBlock()
        );

        $this->addChildBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Table',
                "{$this->getBlockPrefix()}.list.table"
            )->setHeaders($tableHeaders)->setRecords($instanceCollection->load())->addEscapeExcludes(
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
