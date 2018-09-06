<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Invoice
 * @package SuttonBaker\Impresario\Model\Db
 */
class Invoice extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'invoice';
        $this->idColumn = 'invoice_id';

        return $this;
    }

}