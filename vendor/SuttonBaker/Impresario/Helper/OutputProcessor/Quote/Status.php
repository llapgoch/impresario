<?php

namespace SuttonBaker\Impresario\Helper\OutputProcessor\Quote;

class Status
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
        return $this->getQuoteHelper()->getStatusDisplayName($value);
    }
}