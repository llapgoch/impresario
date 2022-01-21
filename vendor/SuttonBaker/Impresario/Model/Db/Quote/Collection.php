<?php

namespace SuttonBaker\Impresario\Model\Db\Quote;
/**
 * Class Collection
 * @package SuttonBaker\Impresario\Model\Db\Client
 */
class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->dbClass = \SuttonBaker\Impresario\Definition\Quote::DEFINITION_MODEL;
        return $this;
    }
}