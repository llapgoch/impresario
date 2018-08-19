<?php

namespace SuttonBaker\Impresario\WP\Layout;

class Horse extends \DaveBaker\Core\WP\Layout\Base
{
    public function bodyContentAction()
    {
        // Create blocks here

        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList */
        $tableList = $this->getApp()->getObjectManager()->get(
            '\SuttonBaker\Impresario\WP\Block\Job\Table',
            ["job.table.four"]
        )->setGoose("Block 4");

        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList2 */
        $tableList2 = $this->getApp()->getObjectManager()->get(
            '\SuttonBaker\Impresario\WP\Block\Job\Table',
            ['job.table.five']
        )->setGoose("Block 5")
            ->setOrder('after', 'job.table.one');

        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList2 */
        $tableList3 = $this->getApp()->getObjectManager()->get(
            '\SuttonBaker\Impresario\WP\Block\Job\Table',
            ['job.table.six']
        )->setGoose("Block 6");



        return [$tableList, $tableList2, $tableList3];
    }

}