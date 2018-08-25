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
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function registerControllers()
    {
        $this->getApp()->getContollerManager()->register([
            "default" => '\SuttonBaker\Impresario\Controller\ClientEditController'
        ]);
    }

    /**
     * @throws \DaveBaker\Core\Layout\Exception
     */
    public function registerLayouts()
    {
        $this->getApp()->getLayoutManager()->register([
            '\SuttonBaker\Impresario\Layout\Client',
        ]);
        
    }




}