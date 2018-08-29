<?php

namespace SuttonBaker\Impresario\Model\Db\Enquiry;

class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->dbClass = \SuttonBaker\Impresario\Definition\Enquiry::DEFINITION_MODEL;
        return $this;
    }
}