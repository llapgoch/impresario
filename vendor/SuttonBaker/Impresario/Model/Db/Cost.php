<?php

namespace SuttonBaker\Impresario\Model\Db;

/**
 * Class Cost
 * @package SuttonBaker\Impresario\Model\Db
 */
class Cost extends Base
{
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
     *
     * @return void
     */
    protected function beforeSave()
    {
        $this->setValue(round($this->getValue(), 2));
        $this->calculatePoItemTotal();
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

        $this->setPoItemTotal($total);

        return $this;
    }
}
