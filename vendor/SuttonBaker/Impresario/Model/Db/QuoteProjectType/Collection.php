<?php

namespace SuttonBaker\Impresario\Model\Db\QuoteProjectType;

/**
 * Class Collection
 * @package SuttonBaker\Impresario\Model\Db\QuoteProjectType
 */
class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->dbClass = \SuttonBaker\Impresario\Model\Db\QuoteProjectType::class;
        return $this;
    }
}
