<?php

namespace SuttonBaker\Impresario\Model\Db\Cost;

/**
 * Class Cost
 * @package SuttonBaker\Impresario\Model\Db
 */
class Item extends \SuttonBaker\Impresario\Model\Db\Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'cost_po_item';
        $this->idColumn = 'po_item_id';

        return $this;
    }

    public function getCost()
    {
        return $this->getCostHelper()->getCost(
            $this->getCostId()
        );
    }


    /**
     *
     * @return void
     */
    protected function beforeSave()
    {
        $this->setUnitPrice(round($this->getUnitPrice(), 2));
        $this->setQty(max(1, (int) $this->getQty()));
        $this->setTotal($this->getQty() * $this->getUnitPrice());
    }

    /**
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function afterSave()
    {
        // if ($parent = $this->getParent()) {
        //     $parent->save();
        // }
    }
}
