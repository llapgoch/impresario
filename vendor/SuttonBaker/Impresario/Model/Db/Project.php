<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Project
 * @package SuttonBaker\Impresario\Model\Db
 */
class Project extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'project';
        $this->idColumn = 'project_id';

        return $this;
    }

    /**
     * @return float
     */
    public function getProfit()
    {
        if($this->hasNetCost() && $this->hasNetSell()){
            return max(0, $this->getNetSell() - $this->getNetCost());
        }

        return 0;
    }
}