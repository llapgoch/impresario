<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Model\Db
 */
class Enquiry extends Base
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

    /**
     * @return bool
     */
    public function isComplete()
    {
        return $this->getStatus() == \SuttonBaker\Impresario\Definition\Enquiry::STATUS_COMPLETE;
    }

}