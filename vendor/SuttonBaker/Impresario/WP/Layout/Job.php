<?php

namespace SuttonBaker\Impresario\WP\Layout;

class Job extends \DaveBaker\Core\WP\Layout\Base
{
    public function defaultAction()
    {

    }
    public function jobListAction()
    {
        // Create blocks here

        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList */
        $tableList = $this->getApp()->getBlockManager()->createBlock(
        '\SuttonBaker\Impresario\WP\Block\Job\Table',
        "job.table.one"
    )->setGoose("Block 1")->setShortcode('body_content');

        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList2 */
        $tableList2 = $this->getApp()->getBlockManager()->createBlock(
            '\SuttonBaker\Impresario\WP\Block\Job\Table',
            "job.table.two"
        )->setGoose("Block 2")->setShortcode('body_content');


        $testInner = $this->getApp()->getBlockManager()->createBlock(
            '\SuttonBaker\Impresario\WP\Block\Test',
            'test.inner'
        )->setTitle("INNER");

        $tableList2->addChildBlock(
            $this->getApp()->getBlockManager()->createBlock(
                '\SuttonBaker\Impresario\WP\Block\Test',
                'test'
            )->setTitle("Child Block")
                ->addChildBlock($testInner)
                ->setColor("red")
        );


//        $this->getApp()->getBlockManager()->removeBlock("job.table.one");
//        $this->getApp()->getBlockManager()->removeBlock("test");

        return [$tableList, $tableList2];
    }
}