<?php

namespace SuttonBaker\Impresario\Controller;

class JobListController
    extends \DaveBaker\Core\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{

    public function execute()
    {
        $job = $this->getApp()->getObjectManager()->getModel('\SuttonBaker\Impresario\Model\Db\Job');
        $job->load(1);

        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

//        $job->setUpdatedAt($helper->getDbTime(1527025178));

        $job->save();
//
        $job = $this->getApp()->getObjectManager()->getModel('\SuttonBaker\Impresario\Model\Db\Job');
        $job->setName('Date test')->save();
    }
}