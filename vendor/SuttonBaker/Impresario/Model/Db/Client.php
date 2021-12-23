<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Model\Db
 */
class Client extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'client';
        $this->idColumn = 'client_id';

        return $this;
    }
}