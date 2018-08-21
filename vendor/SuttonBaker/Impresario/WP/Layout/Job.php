<?php

namespace SuttonBaker\Impresario\WP\Layout;

class Job extends \DaveBaker\Core\WP\Layout\Base
{
    public function defaultAction()
    {
        $this->addBlock($this->getApp()->getBlockManager()->createBlock(
            '\SuttonBaker\Impresario\WP\Block\Test',
            'test.outer'
        )->setTitle("OUTER")
            ->setShortcode('body_content')
            ->addChildBlock($this->getApp()->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'test.inner'
            )->setTitle("INNER")
        ));
    }

    public function registerAction()
    {
        $this->addBlock(
            $this->getApp()->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'login.block'
            )->setTitle("INNER")
            ->setAction('register_form')
        );


        $this->addBlock(
            $this->getApp()->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'login.block2'
            )->setTitle("INNER")
                ->setAction('register_form')
        );

        $this->addBlock(
            $this->getApp()->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'login.block3'
            )->setTitle("INNER")
                ->setAction('register_form')
        );

        
    }

    public function jobListAction()
    {
        // Create blocks here

//        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList */
//        $tableList = $this->getApp()->getBlockManager()->createBlock(
//        '\SuttonBaker\Impresario\WP\Block\Job\Table',
//        "job.table.one"
//    )->setGoose("Block 1")->setShortcode('body_content');
//
//        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList2 */
//        $tableList2 = $this->getApp()->getBlockManager()->createBlock(
//            '\SuttonBaker\Impresario\WP\Block\Job\Table',
//            "job.table.two"
//        )->setGoose("Block 2")->setShortcode('body_content');
//
//
//        $testInner = $this->getApp()->getBlockManager()->createBlock(
//            '\SuttonBaker\Impresario\WP\Block\Test',
//            'test.inner'
//        )->setTitle("INNER");
//
//        $tableList2->addChildBlock(
//            $this->getApp()->getBlockManager()->createBlock(
//                '\SuttonBaker\Impresario\WP\Block\Test',
//                'test'
//            )->setTitle("Child Block")
//                ->addChildBlock($testInner)
//                ->setColor("red")
//        );




        $this->addBlock($this->getApp()->getBlockManager()->createBlock(
            '\SuttonBaker\Impresario\WP\Block\Test',
            'test.action.outer'
        )->setTitle("ACTION OUTER")
            ->setAction('test_action')
            ->addChildBlock($this->getApp()->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'test.action.inner'
            )->setTitle("ACTION INNER"))
            ->addChildBlock($this->getApp()->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'test.action.inner.two'
            )->setTitle("ACTION INNER 2")

        ));

//        $this->getApp()->getBlockManager()->removeBlock("test");


    }
}