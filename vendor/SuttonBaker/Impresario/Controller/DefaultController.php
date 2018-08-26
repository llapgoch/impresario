<?php

namespace SuttonBaker\Impresario\Controller;
/**
 * Class DefaultController
 * @package SuttonBaker\Impresario\Controller
 */
class DefaultController
    extends \DaveBaker\Core\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{

    public function execute()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Client $job */
        $job = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Client')->load(1);
        /** @var \DaveBaker\Core\Helper\Date $date */
        $date = $this->getApp()->getHelper('Date');
//        $job->setName('DB Date Test')->setTest('Test Entry')->save();



    }

    public function callbackBaby($context)
    {
//        var_dump('yeah');
    }

    protected function _postDispatch()
    {
        return parent::_postDispatch();
    }


}