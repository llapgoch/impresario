<?php

namespace SuttonBaker\Impresario\WP\Block\Job;

class Table
    extends \DaveBaker\Core\WP\Block\Base
    implements \DaveBaker\Core\WP\Block\BlockInterface
{
    public function toHtml(){
        return "Block to HTML " . $this->getGoose() . "<br />";
    }

    public function preDispatch()
    {
        parent::preDispatch();
        var_dump("predispatch " . $this->getGoose());
    }

    public function postDispatch()
    {
        parent::postDispatch();

    }
}
