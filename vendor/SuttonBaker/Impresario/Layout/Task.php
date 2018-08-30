<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Task
 * @package SuttonBaker\Impresario\Layout
 */
class Task extends \DaveBaker\Core\Layout\Base
{

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function taskEditHandle()
    {
        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\Form\Edit',
                'task.form.edit'
            )->setElementName('task_edit_form')
                ->setShortcode('body_content')
                ->setFormAction($this->getApp()->getHelper('Url')->getCurrentUrl())

        );
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     */
    public function taskListHandle()
    {
        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\TaskList',
                'task.list'
            )->setShortcode('body_content')
        );
    }
}