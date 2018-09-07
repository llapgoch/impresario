<?php

namespace SuttonBaker\Impresario\Block\Core;
/**
 * Class FlipCard
 * @package SuttonBaker\Impresario\Block\Core
 */
class FlipCard
    extends \DaveBaker\Core\Block\Html\Base
{

    /**
     * @return \DaveBaker\Core\Block\Html\Base
     */
    public function init()
    {
        $this->setTemplate('core/flip-cart.phtml');
        $this->setColour('redbrown');
        return parent::init();
    }
}