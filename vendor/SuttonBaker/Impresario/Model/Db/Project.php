<?php

namespace SuttonBaker\Impresario\Model\Db;

use \SuttonBaker\Impresario\Definition\Invoice as InvoiceDefinition;
use \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;

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
    /** @var Cost\Collection */
    protected $costs;

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
     * @return bool
     */
    public function isComplete()
    {
        return $this->getStatus() == ProjectDefinition::STATUS_COMPLETE;
    }

    /**
     * @param bool $reload
     * @return null|Variation\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getVariations($reload = false)
    {
        if (!$this->getId()) {
            return null;
        }

        if (!$this->variations || $reload) {
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
        if (!$this->getId()) {
            return null;
        }

        if (!$this->invoices || $reload) {
            $this->invoices = $this->getInvoiceHelper()->getInvoiceCollectionForEntity(
                $this->getId(),
                InvoiceDefinition::INVOICE_TYPE_PROJECT
            );
        }

        return $this->invoices;
    }

    /**
     * @param bool $reload
     * @return null|Cost\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getCosts($reload = false)
    {
        if (!$this->getId()) {
            return null;
        }

        if (!$this->costs || $reload) {
            $this->costs = $this->getCostHelper()->getCostCollectionForEntity(
                $this->getId(),
                CostDefinition::COST_TYPE_PROJECT
            );
        }

        return $this->costs;
    }

    public function getOpenPOInvoiceAmountRemaining()
    {
        $total = 0;
        /** @var Cost */
        if($costs = $this->getCosts()) {
            foreach($costs->getItems() as $cost) {
                if(!$cost->isClosed()) {
                    $total += $cost->getInvoiceAmountRemaining();
                }
            }
        }

        return $total;
    }

    /**
     * @return float
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function calculateProfit()
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
    protected function calculateTotalNetCost()
    {
        $netCost = (float) $this->getNetCost();

        if ($this->getVariations()) {
            /** @var Variation $variation */
            foreach ($this->getVariations()->load() as $variation) {
                if ($variation->isApproved()) {
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
    protected function calculateTotalNetSell()
    {
        $netSell = (float) $this->getNetSell();

        if ($this->getVariations()) {
            foreach ($this->getVariations()->load() as $variation) {
                if ($variation->isApproved()) {
                    $netSell += (float)$variation->getValue();
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
    protected function calculateAmountInvoiced()
    {
        $totalInvoiced = 0;

        if ($invoices = $this->getInvoices()) {
            /** @var Invoice $invoice */
            foreach ($invoices->load() as $invoice) {
                $totalInvoiced += (float) $invoice->getValue();
            }
        }

        return $totalInvoiced;
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function calculateInvoiceAmountRemaining()
    {
        return max(0, $this->calculateTotalNetSell() - $this->calculateAmountInvoiced());
    }

    /**
     * @return float|int
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function calculateGp()
    {
        if (!$this->calculateTotalNetSell()) {
            return 0;
        }

        return (float) ($this->calculateProfit() / $this->calculateTotalNetSell()) * 100;
    }

    /**
     * @return null|float
     */
    protected function calculateActualProfit()
    {
        $actualCost = (float) $this->getTotalActualCost();
        $netSell = (float) $this->calculateTotalNetSell();

        if (is_nan($actualCost) || is_nan($netSell)) {
            return null;
        }

        return $netSell - $actualCost;
    }

    /**
     * @return null|float
     */
    public function calculateCostItemTotal()
    {
        $totalCost = 0;

        if ($costs = $this->getCosts()) {
            /** @var Cost $cost */
            foreach ($costs->getItems() as $cost) {
                if ($cost->isClosed()) {
                    $totalCost += (float) $cost->getPoItemTotal();
                }
            }
        }

        return $totalCost;
    }


    /**
     * @return null|float
     */
    protected function calculateTotalActualCost()
    {
        return $this->calculateCostItemTotal() + $this->calculateRebate();
    }

    /**
     * A hacky function to always round up for decimal places (precision) so 1821.7711 becomes 1821.78
     *
     * @param float $value
     * @param integer $precision
     * @return float
     */
    public function calculateCeiling($value, $precision = 0)
    {
        $offset = 0.5;

        if ($precision !== 0) {
            $offset /= pow(10, $precision);
        }

        $final = round($value + $offset, $precision, PHP_ROUND_HALF_DOWN);
        return ($final == -0 ? 0 : $final);
    }

    /**
     *
     * @return float
     */
    public function calculateRebate()
    {
        $rebate = 0;

        if ((bool) $this->getHasRebate()) {
            $rebatePercentage = ((float) $this->getRebatePercentage()) / 100;
            $rebateAddition = $this->calculateCeiling($this->calculateTotalNetSell() * $rebatePercentage, 2);
            $rebate += $rebateAddition;
        }

        return $rebate;
    }

    /**
     * @return null|float
     */
    protected function calculateActualMargin()
    {
        $netSell = $this->calculateTotalNetSell();

        if ($netSell > 0 && is_nan($netSell) === false && ($profit = $this->getActualProfit()) !== null) {
            return ($profit / $netSell) * 100;
        }

        return null;
    }

    protected function beforeSave()
    {
        $this->setData('gp', $this->calculateGp())
            ->setData('total_actual_cost', $this->calculateTotalActualCost())
            ->setData('rebate_amount', $this->calculateRebate())
            ->setData('profit', $this->calculateProfit())
            ->setData('total_net_cost', $this->calculateTotalNetCost())
            ->setData('total_net_sell', $this->calculateTotalNetSell())
            ->setData('amount_invoiced', $this->calculateAmountInvoiced())
            ->setData('invoice_amount_remaining', $this->calculateInvoiceAmountRemaining())
            ->setData('actual_profit', $this->calculateActualProfit())
            ->setData('actual_margin', $this->calculateActualMargin());
    }
}
