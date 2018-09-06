<?php

namespace SuttonBaker\Impresario\Controller\Client;
use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class ClientListController
 * @package SuttonBaker\Impresario\Controller\Client
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_CLIENT,
        Roles::CAP_VIEW_CLIENT,
        Roles::CAP_ALL
    ];

    /**
     * @return \DaveBaker\Core\Controller\Base|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $action = $this->getRequest()->getPostParam('action');

        // Perform client deletes
        if(($clientId = $this->getRequest()->getPostParam('client_id')) && $action == self::DELETE_ACTION){
            /** @var \SuttonBaker\Impresario\Model\Db\Client $client */
            $client = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Client')->load($clientId);

            if(!$client->getId()){
                return;
            }

            $client->setIsDeleted(1)->save();
            $this->addMessage('The client has been removed', Messages::SUCCESS);
            $this->getResponse()->redirectReferer();
        }
    }

    public function execute()
    {
        // TODO: Implement execute() method.
    }
}