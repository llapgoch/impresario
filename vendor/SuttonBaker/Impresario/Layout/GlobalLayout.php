<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Layout
 */
class GlobalLayout extends \DaveBaker\Core\Layout\Base
{
    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function defaultHandle()
    {
        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Structure\Nav',
                'main.sidebar.nav'
            )->setShortcode('impressario_nav_items')
            ->setTemplate('nav/sidebar.phtml')
        );
    }
}