<?php

namespace SuttonBaker\Impresario\Layout;

use DaveBaker\Core\Block\Block;
use SuttonBaker\Impresario\Block\Structure\QuickActions;

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
                'main.header.nav'
            )->setShortcode('impresario_header_nav')
                ->setTemplate('nav/navbar.phtml')
            ->addChildBlock(
                $this->createBlock(
                    QuickActions::class,
                    'main.header.quick.actions',
                    'quickActions'
                )
            )
        );
    }
}