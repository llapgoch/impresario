<?php

namespace SuttonBaker\Impresario;
/**
 * Class Main
 * @package SuttonBaker\Impresario
 */
class Main
    extends \DaveBaker\Core\Main\Base
    implements \DaveBaker\Core\Main\MainInterface
{

    public function init(){}

    /**
     * @throws \DaveBaker\Core\Controller\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerControllers()
    {
        $this->getApp()->getContollerManager()->register([
            'default' => '\SuttonBaker\Impresario\Controller\DefaultController',
            \SuttonBaker\Impresario\Definition\Page::CLIENT_EDIT => '\SuttonBaker\Impresario\Controller\Client\EditController',
            \SuttonBaker\Impresario\Definition\Page::CLIENT_LIST => '\SuttonBaker\Impresario\Controller\Client\ListController',
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_EDIT => '\SuttonBaker\Impresario\Controller\Enquiry\EditController',
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_LIST => '\SuttonBaker\Impresario\Controller\Enquiry\ListController',
            \SuttonBaker\Impresario\Definition\Page::TASK_EDIT=> '\SuttonBaker\Impresario\Controller\Task\EditController',
            \SuttonBaker\Impresario\Definition\Page::TASK_LIST => '\SuttonBaker\Impresario\Controller\Task\ListController',
            \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT => '\SuttonBaker\Impresario\Controller\Quote\EditController',
            \SuttonBaker\Impresario\Definition\Page::QUOTE_LIST => '\SuttonBaker\Impresario\Controller\Quote\ListController'
        ]);
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerInstallers()
    {
        $this->getApp()->getInstallerManager()->register([
            '\SuttonBaker\Impresario\Installer\Client',
            '\SuttonBaker\Impresario\Installer\Enquiry',
            '\SuttonBaker\Impresario\Installer\Task'
        ]);
    }

    /**
     * @throws \DaveBaker\Core\Layout\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerLayouts()
    {
        $this->getApp()->getLayoutManager()->register([
            '\SuttonBaker\Impresario\Layout\Client',
            '\SuttonBaker\Impresario\Layout\Enquiry',
            '\SuttonBaker\Impresario\Layout\Task'
        ]);
        
    }




}