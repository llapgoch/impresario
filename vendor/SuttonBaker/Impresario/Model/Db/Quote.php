<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Model\Db
 */
class Quote extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'quote';
        $this->idColumn = 'quote_id';

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