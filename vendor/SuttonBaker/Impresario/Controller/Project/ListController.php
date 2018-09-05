<?php

namespace SuttonBaker\Impresario\Controller\Project;
use DaveBaker\Core\Definitions\Messages;

/**
 * Class ListController
 * @package SuttonBaker\Impresario\Controller\Project
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';

    /**
     * @return \SuttonBaker\Impresario\Controller\Base|void
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $action = $this->getRequest()->getPostParam('action');

        // Perform deletes
        if(($instanceId = $this->getRequest()->getPostParam('project_id')) && $action == self::DELETE_ACTION){
            /** @var \SuttonBaker\Impresario\Model\Db\Project $instanceObject */
            $instanceObject = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Project')->load($instanceId);

            if(!$instanceObject->getId()){
                return;
            }

            $this->getProjectHelper()->deleteProject($instanceObject);
            $this->addMessage('The project has been removed', Messages::SUCCESS);
            $this->getResponse()->redirectReferer();
        }
    }

    public function execute()
    {

    }
}