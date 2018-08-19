<?php

namespace SuttonBaker\Impresario\WP\Layout;

class Job extends \DaveBaker\Core\WP\Layout\Base
{
    public function bodyContentAction()
    {
        // Create blocks here

        /** @var \SuttonBaker\Impresario\WP\Block\Job\Table $tableList */
        $tableList = $this->getApp()->getObjectManager()->get('\SuttonBaker\Impresario\WP\Block\Job\Table');
        $tableList2 = $this->getApp()->getObjectManager()->get('\SuttonBaker\Impresario\WP\Block\Job\Table');

        return [$tableList, $tableList2];
    }
}