<?php

namespace SuttonBaker\Impresario\WP\Layout;

class Job extends \DaveBaker\Core\WP\Layout\Base
{
    public function defaultHandle()
    {
        $this->addBlock($this->getBlockManager()->createBlock(
            '\SuttonBaker\Impresario\WP\Block\Test',
            'test.outer'
        )->setTitle("OUTER")
            ->setShortcode('body_content')
            ->addChildBlock($this->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'test.inner'
            )->setTitle("INNER 1")
        ));
    }

    public function registerHandle()
    {
        $this->addBlock(
            $this->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'login.block'
            )->setTitle("INNER 1")
            ->setAction('register_form')
                ->setOrder('after', 'login.block2')
        );

        $this->addBlock(
            $this->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'login.block2'
            )->setTitle("INNER 2")
                ->setAction('register_form')
        );

        $this->addBlock(
            $this->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'login.block3'
            )->setTitle("INNER 3")
                ->setAction('register_form')
                ->setOrder('before', 'login.block')
        );
    }

    public function jobListHandle()
    {
        $this->addBlock($this->getBlockManager()->createBlock(
            '\SuttonBaker\Impresario\WP\Block\Test',
            'test.action.outer'
        )->setTitle("ACTION OUTER")->setColor('grey')
            ->setAction('test_action')
            ->addChildBlock($this->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'test.action.inner'
            )->setTitle("ACTION INNER"))
            ->addChildBlock($this->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'test.action.inner.two'
            )->setTitle("ACTION INNER 2")

        ));

//        $this->getApp()->getBlockManager()->removeBlock("test");


    }
}