<?php

namespace SuttonBaker\Impresario\Layout;

use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use SuttonBaker\Impresario\Definition\Page as PageDefinition;
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
    protected $icon = TaskDefinition::ICON;

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
        $taskName = TaskDefinition::getTaskTypeLabel($taskType);

        if($parentItem && $parentItem->getId()){
            $heading = $this->getTaskHelper()->getActionVerb($entityInstance) .
                " Task For " . $taskName .
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

        if(($taskLink = $this->getTaskHelper()->getLinkForParent($parentItem))){
            $mainTile->addChildBlock(
                $createLink = $mainTile->createBlock(
                    '\DaveBaker\Core\Block\Html\ButtonAnchor',
                    'create.enquiry.link',
                    'header_elements'
                )
                    ->setTagText("View $taskName")
                    ->addAttribute(
                        ['href' => $taskLink]
                    )
            );
        }

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
            $taskList = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\TaskList',
                'task.list'
            )->setShortcode('body_content')
        );
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function indexHandle()
    {
        $openTasks = count($this->getTaskHelper()->getOpenTasks()->load());
        $totalTasks = count($this->getTaskHelper()->getTaskCollection()->load());

        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\FlipCard',
                'tasks.flip.card'
            )->setShortcode('body_content')
                ->setTemplate('core/flip-card.phtml')
                ->setIcon(\SuttonBaker\Impresario\Definition\Task::ICON)
                ->setHeading('Open Tasks')
                ->setNumber($openTasks)
                ->setProgressPercentage($this->getTaskHelper()->getPercentage($openTasks, $totalTasks))
                ->setProgressHeading("{$openTasks} open out of {$totalTasks} total tasks")
                ->setColour('cyan')
                ->setBackLink($this->getUrlHelper()->getPageUrl(PageDefinition::TASK_LIST))
                ->setBackText('View Tasks')
                ->setCapabilities($this->getTaskHelper()->getViewCapabilities())
        );
    }
}