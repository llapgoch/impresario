<?php

namespace SuttonBaker\Impresario;

class Main
    extends \DaveBaker\Core\Main\Base
    implements \DaveBaker\Core\Main\MainInterface
{

    public function init() {
        /** \wpdb */
        global $wpdb;

        /** @var \SuttonBaker\Impresario\Model\Db\Job\Collection $job */
//        $job = $this->getApp()->getObjectManager()->getModel('\SuttonBaker\Impresario\Model\Db\Job');

//        $job->getSelect()->where(
//            "name= ? ",
//            'Gary'
//        );
//        echo "<pre>";
//
//
//        foreach($job->load() as $job){
//            var_dump($job->getData());
//        }
//
//        exit;

    }

    public function registerControllers()
    {
        $this->getApp()->getContollerManager()->register([
            "default" => '\SuttonBaker\Impresario\Controller\DefaultController',
            "job_list" => '\SuttonBaker\Impresario\Controller\JobListController'
        ]);
    }

    public function registerLayouts()
    {
        $this->getApp()->getLayoutManager()->register([
            '\SuttonBaker\Impresario\Layout\Job',
            '\SuttonBaker\Impresario\Layout\Quote'
        ]);
        
    }




}