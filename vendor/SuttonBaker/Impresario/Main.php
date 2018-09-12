<?php

namespace SuttonBaker\Impresario;

use \SuttonBaker\Impresario\Definition\Client as ClientDefinition;
use SuttonBaker\Impresario\Definition\Page;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Main
 * @package SuttonBaker\Impresario
 */
class Main
    extends \DaveBaker\Core\Main\Base
    implements \DaveBaker\Core\Main\MainInterface
{

    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function init()
    {
        $this->createAppObject('\SuttonBaker\Impresario\Event\GlobalEvents');
    }

    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerApiActions()
    {
        $api = $this->getApp()->getApiManager();

        $api->addRoute(
            'client',
            '\SuttonBaker\Impresario\Api\Client'
        );

        $api->addRoute(
            'enquiry',
            '\SuttonBaker\Impresario\Api\Enquiry'
        );

        $api->addRoute(
            'quote',
            '\SuttonBaker\Impresario\Api\Quote'
        );

        $api->addRoute(
            'task',
            '\SuttonBaker\Impresario\Api\Task'
        );

        $api->addRoute(
            'project',
            '\SuttonBaker\Impresario\Api\Project'
        );
    }

    /**
     * @throws \DaveBaker\Core\Controller\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerControllers()
    {
        $this->getApp()->getContollerManager()->register([
            \SuttonBaker\Impresario\Definition\Page::CLIENT_EDIT => '\SuttonBaker\Impresario\Controller\Client\EditController',
            \SuttonBaker\Impresario\Definition\Page::CLIENT_LIST => '\SuttonBaker\Impresario\Controller\Client\ListController',
            \SuttonBaker\Impresario\Definition\Page::CLIENT_LIST => '\SuttonBaker\Impresario\Controller\Client\ListController',
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_EDIT => '\SuttonBaker\Impresario\Controller\Enquiry\EditController',
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_LIST => '\SuttonBaker\Impresario\Controller\Enquiry\ListController',
            \SuttonBaker\Impresario\Definition\Page::TASK_EDIT => '\SuttonBaker\Impresario\Controller\Task\EditController',
            \SuttonBaker\Impresario\Definition\Page::TASK_LIST => '\SuttonBaker\Impresario\Controller\Task\ListController',
            \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT => '\SuttonBaker\Impresario\Controller\Quote\EditController',
            \SuttonBaker\Impresario\Definition\Page::QUOTE_LIST => '\SuttonBaker\Impresario\Controller\Quote\ListController',
            \SuttonBaker\Impresario\Definition\Page::PROJECT_EDIT => '\SuttonBaker\Impresario\Controller\Project\EditController',
            \SuttonBaker\Impresario\Definition\Page::PROJECT_LIST => '\SuttonBaker\Impresario\Controller\Project\ListController',
            \SuttonBaker\Impresario\Definition\Page::INVOICE_EDIT => '\SuttonBaker\Impresario\Controller\Invoice\EditController',
            \SuttonBaker\Impresario\Definition\Page::VARIATION_EDIT => '\SuttonBaker\Impresario\Controller\Variation\EditController'
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
            '\SuttonBaker\Impresario\Installer\Task',
            '\SuttonBaker\Impresario\Installer\Quote',
            '\SuttonBaker\Impresario\Installer\Project',
            '\SuttonBaker\Impresario\Installer\InvoiceVariation',
            '\SuttonBaker\Impresario\Installer\General'
        ]);
    }

    /**
     * @throws \DaveBaker\Core\Layout\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerLayouts()
    {
        $this->getApp()->getLayoutManager()->register([
            '\SuttonBaker\Impresario\Layout\GlobalLayout',
            '\SuttonBaker\Impresario\Layout\Client',
            '\SuttonBaker\Impresario\Layout\Enquiry',
            '\SuttonBaker\Impresario\Layout\Task',
            '\SuttonBaker\Impresario\Layout\Quote',
            '\SuttonBaker\Impresario\Layout\Project',
            '\SuttonBaker\Impresario\Layout\Invoice',
            '\SuttonBaker\Impresario\Layout\Variation',
        ]);
        
    }




}