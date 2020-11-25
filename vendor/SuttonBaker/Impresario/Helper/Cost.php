<?php

namespace SuttonBaker\Impresario\Helper;

use \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Cost
 * @package SuttonBaker\Impresario\Helper
 */
class Cost extends Base
{
    /** @var array  */
    protected $editCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_COST];
    protected $viewCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_COST, Roles::CAP_VIEW_COST];

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Cost\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getCostCollection()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Cost\Collection $collection */
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Cost\Collection'
        )->where('is_deleted=?', '0');
        
        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);

        $collection->joinLeft(
            ['created_by_user'=> $userTable],
            "created_by_user.ID={{cost}}.created_by_id",
            ['created_by_name' => 'display_name']
        )->order('{{cost}}.cost_id DESC');


        return $collection;
    }

    /**
     * @param $entityId
     * @param $entityType
     * @return \SuttonBaker\Impresario\Model\Db\Cost\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getCostCollectionForEntity($entityId, $entityType)
    {
        return $this->getCostCollection()
            ->where('cost_type=?', $entityType)
            ->where('parent_id=?', $entityId);
    }



    /**
     * @param string $costType
     * @return mixed|string
     */
    public function getInvoiceTypeDisplayName($costType)
    {
        return $this->getDisplayName($costType, CostDefinition::getCostTypes());
    }

    /**
     * @param $invoiceType
     * @return bool
     */
    public function isValidInvoiceType($costType)
    {
        return in_array($costType, array_keys(CostDefinition::getCostTypes()));
    }

    /**
     * @param $costId
     * @return \SuttonBaker\Impresario\Model\Db\Cost
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getCost($costId = null)
    {
        $invoice = $this->createAppObject(InvoiceDefinition::DEFINITION_MODEL);

        if($invoiceId){
            $invoice->load($invoiceId);
        }

        return $invoice;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Invoice $invoice
     * @return null|\SuttonBaker\Impresario\Model\Db\Enquiry|\SuttonBaker\Impresario\Model\Db\Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getParentForInvoice(
        \SuttonBaker\Impresario\Model\Db\Invoice $invoice
    ) {
        if($invoice->getInvoiceType() == InvoiceDefinition::INVOICE_TYPE_ENQUIRY){
            return $this->getEnquiryHelper()->getEnquiry($invoice->getParentId());
        }

        if($invoice->getInvoiceType() == InvoiceDefinition::INVOICE_TYPE_PROJECT){
            return $this->getProjectHelper()->getProject($invoice->getParentId());
        }

        return null;
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

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Invoice $instance
     * @return mixed|string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function determineInvoiceTypeName(
        \SuttonBaker\Impresario\Model\Db\Invoice $instance
    ) {
        if($instance->getId()){
            return $this->getInvoiceTypeDisplayName($instance->getInvoiceType());
        }

        if($type = $this->getRequest()->getParam('invoice_type')) {
            return $typeName = $this->getInvoiceTypeDisplayName($type);
        }

        return '';
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Invoice $invoice
     * @return $this
     */
    public function deleteInvoice(
        \SuttonBaker\Impresario\Model\Db\Invoice $invoice
    ) {
        if(!$invoice->getId()){
            return $this;
        }

        $invoice->setIsDeleted(1)->save();
        return $this;
    }

}