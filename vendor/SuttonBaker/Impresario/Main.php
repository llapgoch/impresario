<?php

namespace SuttonBaker\Impresario;

class Main
    extends \DaveBaker\Core\WP\Main\Base
    implements \DaveBaker\Core\WP\Main\MainInterface
{

    public function init() {
        /** \wpdb */
        global $wpdb;

        /** @var \SuttonBaker\Impresario\Model\Db\Job\Collection $job */
        $job = $this->getApp()->getObjectManager()->get('\SuttonBaker\Impresario\Model\Db\Job\Collection');

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
            "default" => "\SuttonBaker\Impresario\WP\Controller\DefaultController",
            "job_list" => "\SuttonBaker\Impresario\WP\Controller\JobListController"
        ]);
    }

    public function registerLayouts()
    {
        $this->getApp()->getLayoutManager()->register([
            '\SuttonBaker\Impresario\WP\Layout\Job',
            '\SuttonBaker\Impresario\WP\Layout\Horse'
        ]);
        
    }




}