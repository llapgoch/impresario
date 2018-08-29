<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Model\Db
 */
class Quote extends \DaveBaker\Core\Model\Db\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'quote';
        $this->idColumn = 'quote_id';

        return $this;
    }
}