<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Migration
 * @package SuttonBaker\Impresario\Model\Db
 */
class Migration extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'data_migration';
        $this->idColumn = 'migration_id';

        return $this;
    }
}