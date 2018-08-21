<?php

namespace SuttonBaker\Impresario\WP\Layout;

class Horse extends \DaveBaker\Core\WP\Layout\Base
{
    
    public function jobListAction()
    {
        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList */
        $tableList = $this->getApp()->getBlockManager()->createBlock(
            '\SuttonBaker\Impresario\WP\Block\Job\Table',
            "job.table.sss"
        )->setGoose("Block sss")
            ->setOrder('before', 'job.table.two')
            ->setShortcode('body_content');

//        $this->getApp()->getBlockManager()->removeBlock('test');

        return [$tableList];
    }

}