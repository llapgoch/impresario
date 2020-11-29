<?php

namespace SuttonBaker\Impresario\Helper;

use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Definition\Supplier as SupplierDefinition;

/**
 * Class Supplier
 * @package SuttonBaker\Impresario\Helper
 */
class Supplier extends Base
{
    /** @var array  */
    protected $editCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_SUPPLIER];
    protected $viewCapabilities = [Roles::CAP_ALL, Roles::CAP_VIEW_SUPPLIER, Roles::CAP_EDIT_SUPPLIER];

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Returns a collection of non-deleted suppliers
     */
    public function getSupplierCollection()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Supplier\Collection $collection */
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Supplier\Collection'
        );

        $collection->getSelect()->where('is_deleted=?', '0');
        $collection->getSelect()->order('supplier_name');

        return $collection;
    }

    /**
     * @param int|null $supplierId
     * @return \SuttonBaker\Impresario\Model\Db\Supplier
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getSupplier($supplierId = null)
    {
        $supplier = $this->createAppObject(SupplierDefinition::DEFINITION_MODEL);

        if($supplierId){
            $supplier->load($supplierId);
        }

        return $supplier;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Supplier $supplier
     * @return $this
     */
    public function deleteSupplier(
        \SuttonBaker\Impresario\Model\Db\Supplier $supplier
    ) {
        if(!$supplier->getId()){
            return $this;
        }

        $supplier->setIsDeleted(1)->save();
        return $this;
    }
}