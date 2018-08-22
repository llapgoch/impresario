<?php

namespace SuttonBaker\Impresario\Model\Db;

class Job
    extends \DaveBaker\Core\WP\Model\Db\Base
{
    protected function init()
    {
        $this->tableName = 'job';
        $this->idColumn = 'job_id';
    }
}