<?php

namespace SuttonBaker\Impresario\Model\Db\Variation;
/**
 * Class Collection
 * @package SuttonBaker\Impresario\Model\Db\Variation
 */
class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->dbClass = \SuttonBaker\Impresario\Definition\Variation::DEFINITION_MODEL;
        return $this;
    }
}