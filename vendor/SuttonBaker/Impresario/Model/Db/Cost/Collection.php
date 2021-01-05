<?php

namespace SuttonBaker\Impresario\Model\Db\Cost;
/**
 * Class Collection
 * @package SuttonBaker\Impresario\Model\Db\Cost
 */
class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->dbClass = \SuttonBaker\Impresario\Definition\Cost::DEFINITION_MODEL;
        return $this;
    }
}