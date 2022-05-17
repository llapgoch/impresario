<?php

namespace SuttonBaker\Impresario\Helper;

use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Model\Db\Migration\Collection;

/**
 * Class Role
 * @package SuttonBaker\Impresario\Helper
 */
class Migration extends Base
{
    public function migrationHasRun($code)
    {
        $collection = $this->createAppObject(
            Collection::class
        );

        $collection->where('has_run')
    }
}