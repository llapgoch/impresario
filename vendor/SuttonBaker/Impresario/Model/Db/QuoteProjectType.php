<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class QuoteProjectType
 * @package SuttonBaker\Impresario\Model\Db
 */
class QuoteProjectType extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'quote_project_type';
        $this->idColumn = 'type_id';

        return $this;
    }
}