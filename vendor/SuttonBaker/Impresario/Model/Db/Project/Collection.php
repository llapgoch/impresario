<?php

namespace SuttonBaker\Impresario\Model\Db\Project;
/**
 * Class Collection
 * @package SuttonBaker\Impresario\Model\Db\Project
 */
class Collection extends \DaveBaker\Core\Model\Db\Collection\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->dbClass = \SuttonBaker\Impresario\Definition\Project::DEFINITION_MODEL;
        return $this;
    }
}