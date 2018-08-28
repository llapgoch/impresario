<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Model\Db
 */
class Enquiry extends \DaveBaker\Core\Model\Db\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'enquiry';
        $this->idColumn = 'enquiry_id';

        return $this;
    }
}