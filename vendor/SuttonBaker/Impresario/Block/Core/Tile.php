<?php

namespace SuttonBaker\Impresario\Block\Core;
/**
 * Class Tile
 * @package SuttonBaker\Impresario\Block\Core
 */
class Tile
    extends \DaveBaker\Core\Block\Html\Base
{

    /**
     * @return \DaveBaker\Core\Block\Html\Base|void
     */
    protected function _construct()
    {
        $this->addTagIdentifier('tile');
        parent::_construct();
    }

    /**
     * @return \DaveBaker\Core\Block\Html\Base|void
     */
    public function init()
    {
        parent::init();
        $this->setTemplate('core/tile.phtml');
    }
}