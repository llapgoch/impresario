<?php

namespace SuttonBaker\Impresario\Controller;
/**
 * Class TaskListController
 * @package SuttonBaker\Impresario\Controller
 */
class TaskListController
    extends \DaveBaker\Core\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';

    /**
     * @return \DaveBaker\Core\Controller\Base|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $action = $this->getRequest()->getPostParam('action');

        // Perform task deletes
        if(($instanceId = $this->getRequest()->getPostParam('task_id')) && $action == self::DELETE_ACTION){
            /** @var \SuttonBaker\Impresario\Model\Db\Task $instanceObject */
            $instanceObject = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Task')->load($instanceId);

            if(!$instanceObject->getId()){
                return;
            }

            $instanceObject->setIsDeleted(1)->save();
            $this->addMessage('The task has been removed');
            $this->getResponse()->redirectReferer();
        }
    }

    public function execute()
    {
        // TODO: Implement execute() method.
    }
}