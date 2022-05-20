<?php

namespace SuttonBaker\Impresario\Helper\OutputProcessor\Cost;

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
        return $this->getCostHelper()->getStatusDisplayName($value);
    }
}