<?php

namespace SuttonBaker\Impresario\WP\Layout;

class Job extends \DaveBaker\Core\WP\Layout\Base
{
    public function bodyContentAction()
    {
        // Create blocks here

        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList */
        $tableList = $this->getApp()->getObjectManager()->get(
            '\SuttonBaker\Impresario\WP\Block\Job\Table',
            ["job.table.one"]
        )->setGoose("Block 1");

        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList2 */
        $tableList2 = $this->getApp()->getObjectManager()->get(
            '\SuttonBaker\Impresario\WP\Block\Job\Table',
            ['job.table.two']
        )->setGoose("Block 2");


        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList2 */
        $tableList3 = $this->getApp()->getObjectManager()->get(
            '\SuttonBaker\Impresario\WP\Block\Job\Table',
            ['job.table.three']
        )->setGoose("Block 3");




        return [$tableList, $tableList2, $tableList3];
    }
}