<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Variation
 * @package SuttonBaker\Impresario\Model\Db
 */
class Variation extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'variation';
        $this->idColumn = 'variation_id';

        return $this;
    }

    /**
     * @return float
     */
    public function getProfit()
    {
        if($this->getValue() && $this->hasNetSell()){
            return max(0, $this->getValue() - $this->getNetCost());
        }

        return 0;
    }

    /**
     * @return float|int
     */
    public function getGp()
    {
        if($this->getProfit()){
            return $this->getProfit() / $this->getNetCost();
        }

        return 0;
    }
}