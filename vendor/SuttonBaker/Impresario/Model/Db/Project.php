<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Project
 * @package SuttonBaker\Impresario\Model\Db
 */
class Project extends Base
{
    /** @var Variation\Collection */
    protected $variations;
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
     * @return null|Variation\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getVariations($reload = false)
    {
        if(!$this->getId()){
            return null;
        }

        if(!$this->variations || $reload) {
            $this->variations = $this->getVariationHelper()->getVariationCollectionForProject($this->getId());
        }

        return $this->variations;
    }

    /**
     * @return float
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getProfit()
    {
        return $this->getTotalNetSell() - $this->getTotalNetCost();
    }

    /**
     * @return float
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getTotalNetCost()
    {
        $netCost = (float) $this->getNetCost();

        if($variations = $this->getVariations()->load()){
            foreach($variations as $variation){
                $netCost += (float) $variation->getNetCost();
            }
        }

        return $netCost;
    }

    /**
     * @return float
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getTotalNetSell()
    {
        $netSell = (float) $this->getNetSell();

        if($this->getVariations()) {
            if ($variations = $this->getVariations()->load()) {
                foreach ($variations as $variation) {
                    $netSell += (float)$variation->getValue();
                }
            }
        }

        return $netSell;
    }


    /**
     * @return float|int
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getGp()
    {
        return (float) ($this->getProfit() / $this->getTotalNetSell()) * 100;
    }

    protected function beforeSave()
    {
        $this->setData('gp', $this->getGp());
        $this->setData('profit', $this->getProfit());
        $this->setData('total_net_cost', $this->getTotalNetCost());
        $this->setData('total_net_sell', $this->getTotalNetSell());
    }
}