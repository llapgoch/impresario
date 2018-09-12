<?php

namespace SuttonBaker\Impresario\Block\Core\Tile;
use SuttonBaker\Impresario\Block\Core\Tile;

/**
 * Class Black
 * @package SuttonBaker\Impresario\Block\Core
 */
class White
    extends Tile
{

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->addTagIdentifier(['tile', 'tile-white']);
        parent::_construct();
    }
}