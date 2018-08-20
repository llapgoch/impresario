<?php

namespace SuttonBaker\Impresario\WP\Block;

class Test extends \DaveBaker\Core\WP\Block\Template\Base
{
    public function init()
    {
        $this->setColor('blue');
        $this->setTemplate('/test/span.phtml');
    }

    public function preDispatch()
    {
        return parent::preDispatch();
    }
}