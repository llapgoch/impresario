<?php

namespace SuttonBaker\Impresario\Block;

class Test extends \DaveBaker\Core\Block\Template
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