<?php

namespace SuttonBaker\Impresario\Helper;

use SuttonBaker\Impresario\Definition\Migration as DefinitionMigration;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Model\Db\Migration\Collection;

/**
 * Class Role
 * @package SuttonBaker\Impresario\Helper
 */
class Migration extends Base
{
    /**
     *
     * @param string $code
     * @return bool
     */
    public function migrationHasRun($code)
    {
        $migration = $this->getMigrationByCode($code);
        return (bool) $migration->getHasRun();
    }

    /**
     * @param $code
     * @return \SuttonBaker\Impresario\Model\Db\Migration
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getMigrationByCode($code = null)
    {
        $migration = $this->createAppObject(DefinitionMigration::DEFINITION_MODEL);

        if ($migration) {
            $migration->load($code, 'code');
        }

        return $migration;
    }
}
