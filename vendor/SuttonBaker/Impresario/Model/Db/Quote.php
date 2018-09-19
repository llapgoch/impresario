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
        if(is_numeric($this->getNetSell()) && is_numeric($this->getNetCost())){
            return $this->getNetSell() - $this->getNetCost();
        }

        return 0;
    }

    /**
     * @return float
     */
    public function getGp()
    {
        if(is_numeric($this->getNetSell()) && is_numeric($this->getNetCost())){
            return ($this->getProfit() / $this->getNetSell()) * 100;
        }

        return 0;
    }

    public function beforeSave()
    {
        $this->setData('gp', $this->getGp());
        $this->setData('profit', $this->getProfit());
    }
}