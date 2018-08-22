<?php

namespace SuttonBaker\Impresario\WP\Block;

class Test extends \DaveBaker\Core\WP\Block\Template
{
    public function init()
    {
        $this->setColor('red');
        $this->setTemplate('/test/span.phtml');
    }

    protected function _preDispatch()
    {
        return parent::_preDispatch();
    }

    protected function _postDispatch()
    {
        return parent::_preDispatch();
    }
}