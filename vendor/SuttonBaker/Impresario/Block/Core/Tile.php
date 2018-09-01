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
     * @return \DaveBaker\Core\Block\Html\Base
     */
    public function init()
    {
        $this->setTemplate('core/tile.phtml');
        $this->addTagIdentifier('tile');
        return parent::init();
    }
}