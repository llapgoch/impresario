<?php

namespace SuttonBaker\Impresario\Model\Db\Invoice;
/**
 * Class Collection
 * @package SuttonBaker\Impresario\Model\Db\Invoice
 */
class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->dbClass = \SuttonBaker\Impresario\Definition\Invoice::DEFINITION_MODEL;
        return $this;
    }
}