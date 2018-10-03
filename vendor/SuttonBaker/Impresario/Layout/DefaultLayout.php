<?php

namespace SuttonBaker\Impresario\Layout;

use DaveBaker\Core\Block\Block;
/**
 * Class DefaultLayout
 * @package SuttonBaker\Impresario\Layout
 */
class DefaultLayout
    extends Base
{
    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function defaultHandle()
    {
//        $this->addBlock(
//            $this->createBlock(
//                '\SuttonBaker\Impresario\Block\Structure\Nav',
//                'main.sidebar.nav'
//            )->setShortcode('impressario_nav_items')
//                ->setTemplate('nav/sidebar.phtml')
//        );

        $this->addBlock(
            $this->rootContainer = $this->createBlock(
                Block::class,
                'root.container'
            )->setShortcode('root')
        );

        $this->rootContainer->addChildBlock(
            $this->getModalHelper()->createModalPlaceholder()
        );

        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Structure\Nav',
                'main.sidebar.nav'
            )->setShortcode('impresario_header_nav')
                ->setTemplate('nav/navbar.phtml')
        );
    }
}