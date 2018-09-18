<?php

namespace SuttonBaker\Impresario\Controller;
/**
 * Class DefaultController
 * @package SuttonBaker\Impresario\Controller
 */
class DefaultController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    // Add this to the default handle so all pages require a user to be logged in

    /**
     * @return Base|void
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function preDispatch()
    {
        if(!$this->getRequest()->isAjax() || $this->getRequest()->isRest()){
            $this->requiresLogin = true;
        }

        parent::preDispatch();
    }

    public function execute()
    {

    }
}