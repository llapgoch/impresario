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
            \SuttonBaker\Impresario\Definition\Page::CLIENT_EDIT => '\SuttonBaker\Impresario\Controller\ClientEditController',
            \SuttonBaker\Impresario\Definition\Page::CLIENT_LIST => '\SuttonBaker\Impresario\Controller\ClientListController',
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_EDIT => '\SuttonBaker\Impresario\Controller\EnquiryEditController',
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_LIST => '\SuttonBaker\Impresario\Controller\EnquiryListController'
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
            '\SuttonBaker\Impresario\Installer\Enquiry'
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
            '\SuttonBaker\Impresario\Layout\Enquiry'
        ]);
        
    }




}