<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Invoice
 * @package SuttonBaker\Impresario\Model\Db
 */
class Invoice extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'invoice';
        $this->idColumn = 'invoice_id';

        return $this;
    }

    /**
     * @return null|Enquiry|Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getParent()
    {
        return $this->getInvoiceHelper()->getParentForInvoice($this);
    }

    /**
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function afterSave()
    {
        if($parent = $this->getParent()){
            $parent->save();
        }
    }

}