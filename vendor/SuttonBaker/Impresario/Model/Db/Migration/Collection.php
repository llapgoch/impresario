<?php

namespace SuttonBaker\Impresario\Model\Db\Migration;

use SuttonBaker\Impresario\Model\Db\Migration;

class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->dbClass = Migration::class;
        return $this;
    }
}