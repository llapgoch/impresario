<?php

namespace SuttonBaker\Impresario\Model\Db\Task;

class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->dbClass = \SuttonBaker\Impresario\Definition\Task::DEFINITION_MODEL;
        return $this;
    }
}