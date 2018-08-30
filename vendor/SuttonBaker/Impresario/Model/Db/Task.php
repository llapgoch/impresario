<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Task
 * @package SuttonBaker\Impresario\Model\Db
 */
class Task extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'task';
        $this->idColumn = 'task_id';

        return $this;
    }
}