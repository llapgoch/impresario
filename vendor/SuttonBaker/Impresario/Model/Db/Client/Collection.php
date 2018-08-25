<?php

namespace SuttonBaker\Impresario\Model\Db\Client;

class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->dbClass = '\SuttonBaker\Impresario\Model\Db\Client';
        return $this;
    }
}