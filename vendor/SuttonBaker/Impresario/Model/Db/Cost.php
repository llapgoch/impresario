<?php

namespace SuttonBaker\Impresario\Model\Db;

use SuttonBaker\Impresario\Definition\Invoice;
use SuttonBaker\Impresario\Model\Db\Invoice as DbInvoice;

/**
 * Class Cost
 * @package SuttonBaker\Impresario\Model\Db
 */
class Cost extends Base
{
    /** @var array */
    protected $invoices;

    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'cost';
        $this->idColumn = 'cost_id';

        return $this;
    }

    /**
     * @return null|Enquiry|Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getParent()
    {
        return $this->getCostHelper()->getParentForCost($this);
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
                Invoice::INVOICE_TYPE_PO_INVOICE
            );
        }

        return $this->invoices;
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
            /** @var DbInvoice $invoice */
            foreach ($invoices->load() as $invoice) {
                $totalInvoiced += (float) $invoice->getValue();
            }
        }

        return $totalInvoiced;
    }



    /**
     *
     * @return void
     */
    protected function beforeSave()
    {
        $this->setValue(round($this->getValue(), 2));

        $this->setData('po_item_total', $this->calculatePoItemTotal())
            ->setData('amount_invoiced', $this->calculateAmountInvoiced())
            ->setData('invoice_amount_remaining', $this->calculateInvoiceAmountRemaining());
    }

    /**
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function afterSave()
    {
        if ($parent = $this->getParent()) {
            $parent->save();
        }
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
        return max(0, $this->calculatePoItemTotal() - $this->calculateAmountInvoiced());
    }

    public function calculatePoItemTotal()
    {
        $total = 0;

        if ($this->getId()) {
            // Always load a fresh collection when saving
            $items = $this->getCostHelper()->getCostInvoiceItems($this->getId(), true)->getItems();

            foreach ($items as $item) {
                $total += (float) $item->getTotal();
            }
        }

        return $total;
    }
}
