<?php

namespace SuttonBaker\Impresario\Controller\Task;
use DaveBaker\Core\Definitions\Messages;

/**
 * Class ListController
 * @package SuttonBaker\Impresario\Controller\Task
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';

    /**
     * @return \DaveBaker\Core\Controller\Base|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {

        $this->addEvent('block_predispatch_before_task_list_table', function($context){
            $context->getObject()->setShowSuperseeded(false);
        });


        $action = $this->getRequest()->getPostParam('action');

        // Perform task deletes
        if(($instanceId = $this->getRequest()->getPostParam('task_id')) && $action == self::DELETE_ACTION){
            /** @var \SuttonBaker\Impresario\Model\Db\Task $instanceObject */
            $instanceObject = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Task')->load($instanceId);

            if(!$instanceObject->getId()){
                return;
            }

            $instanceObject->setIsDeleted(1)->save();
            $this->addMessage('The task has been removed', Messages::SUCCESS);
            $this->getResponse()->redirectReferer();
        }
    }

    public function execute()
    {
//        if($taskTable = $this->getApp()->getBlockManager()->getBlock('task.list.table')){
//            $taskTable->setShowsuperseded(false);
//        }
    }
}