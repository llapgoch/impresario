<?php

namespace SuttonBaker\Impresario\Controller\Quote;
use DaveBaker\Core\Definitions\Messages;

/**
 * Class ListController
 * @package SuttonBaker\Impresario\Controller\Quote
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
        $action = $this->getRequest()->getPostParam('action');

        // Perform deletes
        if(($instanceId = $this->getRequest()->getPostParam('quote_id')) && $action == self::DELETE_ACTION){
            /** @var \SuttonBaker\Impresario\Model\Db\Quote $instanceObject */
            $instanceObject = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Quote')->load($instanceId);

            if(!$instanceObject->getId()){
                return;
            }

            $instanceObject->setIsDeleted(1)->save();
            $this->addMessage('The quote has been removed', Messages::SUCCESS);
            $this->getResponse()->redirectReferer();
        }
    }

    public function execute()
    {
        // TODO: Implement execute() method.
    }
}