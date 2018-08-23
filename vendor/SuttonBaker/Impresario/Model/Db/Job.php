<?php

namespace SuttonBaker\Impresario\Model\Db;

class Job extends \DaveBaker\Core\Model\Db\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'job';
        $this->idColumn = 'job_id';

        return $this;
    }
}