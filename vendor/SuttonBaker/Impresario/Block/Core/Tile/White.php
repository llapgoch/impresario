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
     * @return \DaveBaker\Core\Block\Html\Base
     */
    public function init()
    {
        parent::init();
        $this->addTagIdentifier('tile-white');
    }
}