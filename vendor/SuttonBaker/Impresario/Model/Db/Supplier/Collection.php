<?php

namespace SuttonBaker\Impresario\Model\Db\Supplier;
/**
 * Class Collection
 * @package SuttonBaker\Impresario\Model\Db\Supplier
 */
class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->dbClass = '\SuttonBaker\Impresario\Model\Db\Supplier';
        return $this;
    }
}