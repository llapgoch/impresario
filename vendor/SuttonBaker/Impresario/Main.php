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


    public function init() {

    }

    public function registerControllers()
    {
//        $this->getApp()->getContollerManager()->register([
//            "default" => '\SuttonBaker\Impresario\Controller\DefaultController',
//            "job_list" => '\SuttonBaker\Impresario\Controller\JobListController'
//        ]);
    }

    public function registerLayouts()
    {
        $this->getApp()->getLayoutManager()->register([
            '\SuttonBaker\Impresario\Layout\Client',
        ]);
        
    }




}