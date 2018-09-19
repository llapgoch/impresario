<?php

namespace SuttonBaker\Impresario\Helper\OutputProcessor\Quote;

/**
 * Class TenderStatus
 * @package SuttonBaker\Impresario\Helper\OutputProcessor\Quote
 */
class TenderStatus
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
        return $this->getQuoteHelper()->getTenderStatusDisplayName($value);
    }
}