<?php

namespace SuttonBaker\Impresario\Block\Core\Tile;
use SuttonBaker\Impresario\Block\Core\Tile;
/**
 * Class Black
 * @package SuttonBaker\Impresario\Block\Core
 */
class Black
    extends Tile
{

    /**
     * @return \DaveBaker\Core\Block\Html\Base|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->addTagIdentifier(['tile', 'tile-black']);
        parent::_construct();
    }
}