<?php

namespace SuttonBaker\Impresario\Block\Job;

class Table
    extends \DaveBaker\Core\Block\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    public function toHtml(){
        return "Block to HTML " . $this->getGoose() . "<br />";
    }

    public function preDispatch()
    {
        parent::preDispatch();
//        var_dump("predispatch " . $this->getGoose());
    }

    public function postDispatch()
    {
        parent::postDispatch();

    }
}
