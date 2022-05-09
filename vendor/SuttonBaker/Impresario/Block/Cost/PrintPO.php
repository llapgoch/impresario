<?php

namespace SuttonBaker\Impresario\Block\Cost;

use Exception;
use SuttonBaker\Impresario\Model\Db\Cost;

class PrintPO extends \DaveBaker\Core\Block\Template
{
    protected $template = 'cost-po/print.phtml';

    /** @var Cost */
    protected $cost;

    /**
     * 
     *
     * @param Cost $cost
     * @return $this
     */
    public function setCost(Cost $cost)
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     *
     * @return Cost
     */
    public function getCost()
    {
        if(!$this->cost) {
            throw new Exception("Cost not set");
        }
        return $this->cost;
    }

    public function getCostDate()
    {
        
    }

    /**
     *
     * @return string
     */
    public function getSupplierAddress()
    {
        $supplier = $this->getSupplierHelper()->getSupplier($this->getCost()->getSupplierId());

        if(!$supplier->getId()) {
            return '- -';
        }

        $addressParts = [
            $supplier->getSupplierName(),
            $supplier->getAddressLine1(),
            $supplier->getAddressLine2(),
            $supplier->getAddressLine3(),
            $supplier->getCounty(),
            $supplier->getPostcode(),
        ];

        return implode(",<br />", array_filter($addressParts));
    }


    /**
     * @return \SuttonBaker\Impresario\Helper\Supplier
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getSupplierHelper()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\Supplier::class);
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return get_stylesheet_directory_uri() . '/assets/images/logo.svg';
    }
}
