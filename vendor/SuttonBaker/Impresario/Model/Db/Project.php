<?php

namespace SuttonBaker\Impresario\Model\Db;

use \SuttonBaker\Impresario\Definition\Invoice as InvoiceDefinition;
/**
 * Class Project
 * @package SuttonBaker\Impresario\Model\Db
 */
class Project extends Base
{
    /** @var Variation\Collection */
    protected $variations;
    /** @var Invoice\Collection */
    protected $invoices;
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
     * @param bool $reload
     * @return null|Variation\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
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
     * @param $reload
     * @return null|Invoice\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getInvoices($reload = false)
    {
        if(!$this->getId()){
            return null;
        }

        if(!$this->invoices || $reload) {
            $this->invoices = $this->getInvoiceHelper()->getInvoiceCollectionForEntity(
                $this->getId(), InvoiceDefinition::INVOICE_TYPE_PROJECT);
        }

        return $this->invoices;
    }

    /**
     * @return float
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function calculateProfit()
    {
        return $this->calculateTotalNetSell() - $this->calculateTotalNetCost();
    }

    /**
     * @return float
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function calculateTotalNetCost()
    {
        $netCost = (float) $this->getNetCost();

        if($variations = $this->getVariations()->load()) {
            /** @var Variation $variation */
            foreach ($variations as $variation) {
                if ($variation->isApproved()){
                    $netCost += (float)$variation->getNetCost();
                }
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
    public function calculateTotalNetSell()
    {
        $netSell = (float) $this->getNetSell();

        if($this->getVariations()) {
            if ($variations = $this->getVariations()->load()) {
                foreach ($variations as $variation) {
                    if ($variation->isApproved()) {
                        $netSell += (float)$variation->getValue();
                    }
                }
            }
        }

        return $netSell;
    }

    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    function calculateAmountInvoiced()
    {
        $totalInvoiced = 0;

        if($invoices = $this->getInvoices()){
            /** @var Invoice $invoice */
            foreach($invoices->load() as $invoice){
                $totalInvoiced += (float) $invoice->getValue();
            }
        }

        return $totalInvoiced;
    }


    /**
     * @return float|int
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function calculateGp()
    {
        return (float) ($this->calculateProfit() / $this->calculateTotalNetSell()) * 100;
    }

    protected function beforeSave()
    {
        $this->setData('gp', $this->calculateGp());
        $this->setData('profit', $this->calculateProfit());
        $this->setData('total_net_cost', $this->calculateTotalNetCost());
        $this->setData('total_net_sell', $this->calculateTotalNetSell());
        $this->setData('amount_invoiced', $this->calculateAmountInvoiced());
    }
}