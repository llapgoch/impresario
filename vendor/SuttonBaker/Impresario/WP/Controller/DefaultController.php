<?php

namespace SuttonBaker\Impresario\WP\Controller;

class DefaultController
    extends \DaveBaker\Core\WP\Controller\Base
    implements \DaveBaker\Core\WP\Controller\ControllerInterface
{

    public function execute()
    {
        var_dump("executing default controller");

        $blocks = $this->getApp()->getBlockManager()->getAllRenderedBlocks();
        $allBlocks = $this->getApp()->getBlockManager()->getAllBlocks();

        var_dump(count($blocks));
        var_dump(count($allBlocks));
    }

    protected function _postDispatch()
    {
        var_dump("POST DISPATCH");
        var_dump(count($this->getApp()->getBlockManager()->getAllRenderedBlocks()));
        return parent::_postDispatch();
    }


}