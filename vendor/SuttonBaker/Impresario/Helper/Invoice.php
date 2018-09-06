<?php

namespace SuttonBaker\Impresario\Helper;

use \SuttonBaker\Impresario\Definition\Invoice as InvoiceDefinition;
/**
 * Class Invoice
 * @package SuttonBaker\Impresario\Helper
 */
class Invoice extends Base
{

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Invoice\Collection
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Returns a collection of non-deleted invoices
     */
    public function getInvoiceCollection()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Invoice\Collection $collection */
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Invoice\Collection'
        )->where('is_deleted=?', '0');

        /** @var \Zend_Db_Select $select */
        $select = $collection->getSelect();
        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);


        $collection->joinLeft(
            ['created_by_user'=> $userTable],
            "created_by_user.ID={{invoice}}.created_by_id",
            ['created_by_name' => 'user_login']
        )->order('{{invoice}}.invoice_id DESC');


        return $collection;
    }

    /**
     * @param $entityId
     * @param $entityType
     * @return \SuttonBaker\Impresario\Model\Db\Invoice\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getInvoiceCollectionForEntity($entityId, $entityType)
    {
        return $this->getInvoiceCollection()
            ->where('invoice_type=?', $entityType)
            ->where('parent_id=?', $entityId);
    }



    /**
     * @param string $invoiceType
     * @return mixed|string
     */
    public function getInvoiceTypeDisplayName($invoiceType)
    {
        return $this->getDisplayName($invoiceType, InvoiceDefinition::getInvoiceTypes());
    }

    /**
     * @param $invoiceType
     * @return bool
     */
    public function isValidInvoiceType($invoiceType)
    {
        return in_array($invoiceType, array_keys(InvoiceDefinition::getInvoiceTypes()));
    }

    /**
     * @param $invoiceId
     * @return \SuttonBaker\Impresario\Model\Db\Invoice
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getInvoice($invoiceId = null)
    {
        $invoice = $this->createAppObject(InvoiceDefinition::DEFINITION_MODEL);

        if($invoiceId){
            $invoice->load($invoiceId);
        }

        return $invoice;
    }

    /**
     * @param $parentInstance
     * @return string
     */
    public function getInvoiceTypeForParent($parentInstance)
    {
        if($parentInstance instanceof \SuttonBaker\Impresario\Model\Db\Enquiry){
            return InvoiceDefinition::INVOICE_TYPE_ENQUIRY;
        }

        if($parentInstance instanceof \SuttonBaker\Impresario\Model\Db\Project){
            return InvoiceDefinition::INVOICE_TYPE_PROJECT;
        }

        return null;
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\OutputProcessor\Invoice\Type
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getInvoiceTypeOutputProcessor()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\OutputProcessor\Invoice\Type');
    }

}