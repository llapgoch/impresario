<?php

namespace SuttonBaker\Impresario\WP\Block;

class Test extends \DaveBaker\Core\WP\Block\Template
{
    public function init()
    {
        $this->setColor('red');
        $this->setTemplate('/test/span.phtml');
    }

    public function preDispatch()
    {
        return parent::preDispatch();
    }
}