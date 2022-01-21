<?php

namespace SuttonBaker\Impresario\Helper\OutputProcessor\Invoice;

/**
 * Class Type
 * @package SuttonBaker\Impresario\Helper\OutputProcessor\Invoice
 */
class Type
    extends \SuttonBaker\Impresario\Helper\OutputProcessor\Base
    implements \DaveBaker\Core\Helper\OutputProcessor\OutputProcessorInterface
{
    /**
     * @param $value
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function process($value)
    {
        return $this->getInvoiceHelper()->getInvoiceTypeDisplayName($value);
    }
}