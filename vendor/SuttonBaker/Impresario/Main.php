<?php

namespace SuttonBaker\Impresario;

class Main
{
    protected $app;

    public function __construct(
      \DaveBaker\Core\App $app
    ) {
        $this->app = $app;

        /** @var \SuttonBaker\Impresario\Model\Db\Job $job */
        $job = $app->getObjectManager()->get('\SuttonBaker\Impresario\Model\Db\Job');

        $job->load(1);

        $job->setName("Goose");
        $job->setTest(101);
        $job->setDateCreated("2018-01-01");
        $job->save();

        /** @var \SuttonBaker\Impresario\Model\Db\Job\Collection $jobCollection */
        $jobCollection = $app->getObjectManager()->get('\SuttonBaker\Impresario\Model\Db\Job\Collection');
        $jobCollection->getSelect()->order("name ASC");
        $items = $jobCollection->load();



    }


}