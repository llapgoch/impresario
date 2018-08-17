<?php

namespace SuttonBaker\Impresario\Model\Db\Job;

class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    protected function init()
    {
        $this->dbClass = '\SuttonBaker\Impresario\Model\Db\Job';
    }
}