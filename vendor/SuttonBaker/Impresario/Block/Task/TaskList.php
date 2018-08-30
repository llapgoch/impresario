<?php

namespace SuttonBaker\Impresario\Block\Task;

use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
/**
 * Class TaskList
 * @package SuttonBaker\Impresario\Block\Task
 */
class TaskList
    extends \SuttonBaker\Impresario\Block\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    const BLOCK_PREFIX = 'task';
    const COMPLETED_KEY = 'completed';
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
        /** @var \SuttonBaker\Impresario\Model\Db\Task\Collection $instanceCollection */
        $instanceCollection = $this->getTaskHelper()->getTaskCollection();

        if($this->getRequest()->getParam(self::COMPLETED_KEY)){
            $instanceCollection->getSelect()->where(
                'status=?',
                \SuttonBaker\Impresario\Definition\Task::STATUS_COMPLETE
            );
        }



        $instanceItems = $instanceCollection->load();

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


        if(count($instanceItems)) {
            $tableHeaders = TaskDefinition::TABLE_HEADERS;
            // add edit for each one
            foreach($instanceItems as $instanceItem){
                $instanceItem->setData('edit_column',  $this->getLinkHtml($instanceItem));

                $instanceItem->setData('delete_column', $this->getDeleteBlockHtml($instanceItem->getId()));


                if($value = $instanceItem->getTargetDate()) {
                    $instanceItem->setTargetDate(
                        $this->getApp()->getHelper('Date')->utcDbDateToShortLocalOutput($value)
                    );
                }

                if($value = $instanceItem->getPriority()){
                    $instanceItem->setPriority($this->getTaskHelper()->getPriorityDisplayName($value));
                }

                if($value = $instanceItem->getTaskType()){
                    $instanceItem->setTaskType($this->getTaskHelper()->getTaskTypeDisplayName($value));
                }

                if($value = $instanceItem->getStatus()){
                    $instanceItem->setStatus($this->getTaskHelper()->getStatusDisplayName($value));
                }
            }

            $this->addChildBlock(
                $this->createBlock(
                    '\DaveBaker\Core\Block\Html\Table',
                    "{$this->getBlockPrefix()}.list.table"
                )->setHeaders($tableHeaders)->setRecords($instanceItems)->addEscapeExcludes(['edit_column', 'delete_column'])
            );
        }
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Task $instance
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getEditUrl(\SuttonBaker\Impresario\Model\Db\Task $instance)
    {
        return $this->getApp()->getHelper('Url')->getPageUrl(
            \SuttonBaker\Impresario\Definition\Page::TASK_EDIT,
            ['task_id' => $instance->getId()]
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
     * @param \SuttonBaker\Impresario\Model\Db\Task $instance
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getLinkHtml(\SuttonBaker\Impresario\Model\Db\Task $instance)
    {
        return "<a href={$this->getEditUrl($instance)}>" . $this->escapeHtml('Edit Task') . "</a>";
    }

    /**
     * @param $instanceId
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getDeleteBlockHtml($instanceId)
    {
        /** @var \DaveBaker\Form\Block\Form $form */
        $form = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Form', "{$this->getBlockPrefix()}.list.delete.{$instanceId}")
            ->setElementName("{$this->getBlockPrefix()}_delete");

        /** @var \DaveBaker\Form\Block\Input\Submit $submit */
        $submit = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Submit', "{$this->getBlockPrefix()}.list.delete.submit.{$instanceId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $id = $this->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "{$this->getBlockPrefix()}.list.delete.id.{$instanceId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $action = $this->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "{$this->getBlockPrefix()}.list.delete.action.{$instanceId}");

        $submit->setElementName('submit')
            ->setElementValue("Delete");

        $id->setElementValue($instanceId)->setElementName("{$this->getBlockPrefix()}_id");
        $action->setElementName('action')->setElementValue('delete');

        $form->addChildBlock([$submit, $id, $action]);

        return $form->render();
    }

}
