<?php

namespace SuttonBaker\Impresario\Helper\OutputProcessor\Variation;

/**
 * Class Status
 * @package SuttonBaker\Impresario\Helper\OutputProcessor\Variation
 */
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
        return $this->getVariationHelper()->getStatusDisplayName($value);
    }
}