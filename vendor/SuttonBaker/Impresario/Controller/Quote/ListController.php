<?php

namespace SuttonBaker\Impresario\Controller\Quote;
use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class ListController
 * @package SuttonBaker\Impresario\Controller\Quote
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_QUOTE,
        Roles::CAP_VIEW_QUOTE,
        Roles::CAP_ALL
    ];

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
        if(($instanceId = $this->getRequest()->getPostParam('quote_id')) && $action == self::DELETE_ACTION){
            /** @var \SuttonBaker\Impresario\Model\Db\Quote $instanceObject */
            $instanceObject = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Quote')->load($instanceId);

            if(!$instanceObject->getId()){
                return;
            }

            $this->getQuoteHelper()->deleteQuote($instanceObject);
            $this->addMessage('The quote has been removed', Messages::SUCCESS);
            $this->getResponse()->redirectReferer();
        }
    }

    public function execute()
    {

    }
}