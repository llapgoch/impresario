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
     * @return \SuttonBaker\Impresario\Block\ListBase|void
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
                '\SuttonBaker\Impresario\Block\Component\ActionBar',
                "{$this->getBlockPrefix()}.list.action.bar"
            )->addActionItem(
                'All Tasks',
                $this->getPageUrl(PageDefinition::TASK_LIST)
            )->addActionItem(
                'Completed Tasks',
                $this->getPageUrl(PageDefinition::TASK_LIST, ['completed' => 1])
            )
        );

        $this->addChildBlock($this->getMessagesBlock());

        $this->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\TaskTable',
                "{$this->getBlockPrefix()}.list.table"
            )->setHeaders(TaskDefinition::TABLE_HEADERS)->setRecords($this->instanceCollection->load())->addEscapeExcludes(
                ['edit_column', 'delete_column']
            )
        );
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Task\Collection
     */
    public function getInstanceCollection()
    {
        return $this->instanceCollection;
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
