<?php

namespace SuttonBaker\Impresario\Layout;

use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
/**
 * Class Task
 * @package SuttonBaker\Impresario\Layout
 */
class Task extends Base
{
    const ID_KEY = 'task_id';
    /** @var string  */
    protected $blockPrefix = 'task';
    protected $headingName = 'Tasks';
    protected $icon = 'fa-th-list';

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function taskEditHandle()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Task $entityInstance */
        $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Task');

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            $entityInstance->load($entityId);
            $editMode = true;
        }

        $this->addHeading()->addMessages();

        $entityInstance = $this->getApp()->getRegistry()->get('model_instance');
        $parentItem = $this->getApp()->getRegistry()->get('parent_item');
        $taskType = $this->getApp()->getRegistry()->get('task_type');

        if($parentItem && $parentItem->getId()){
            $heading = ($entityInstance->getId() ? "Update" : "Create") .
                " Task For " . TaskDefinition::getTaskTypeLabel($taskType) .
                " '{$parentItem->getSiteName()}'";
        }

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading($heading)
                ->setShortcode('body_content')
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName("{$this->getBlockPrefix()}_edit_form")

        );

    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function taskListHandle()
    {
        $this->addHeading()->addMessages();

        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\TaskList',
                'task.list'
            )->setShortcode('body_content')
        );
    }
}