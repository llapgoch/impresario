<?php

namespace SuttonBaker\Impresario\Helper\OutputProcessor;

abstract class Base
    extends \DaveBaker\Core\Helper\OutputProcessor\Base
{
    /**
     * @return \SuttonBaker\Impresario\Helper\Task
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getTaskHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Task');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getEnquiryHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Enquiry');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getClientHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Client');
    }


    /**
     * @return \SuttonBaker\Impresario\Helper\Supplier
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getSupplierHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Supplier');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Quote
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getQuoteHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Quote');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getProjectHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Project');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Invoice
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getInvoiceHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Invoice');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Variation
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getVariationHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Variation');
    }
}