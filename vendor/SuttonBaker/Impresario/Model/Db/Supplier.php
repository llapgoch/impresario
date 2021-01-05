<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Supplier
 * @package SuttonBaker\Impresario\Model\Db
 */
class Supplier extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'supplier';
        $this->idColumn = 'supplier_id';

        return $this;
    }
}