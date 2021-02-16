<?php

namespace SuttonBaker\Impresario\Installer;

use \SuttonBaker\Impresario\Definition\Cost as CostDefinition;

/**
 * Class InvoiceVariation
 * @package SuttonBaker\Impresario\Installer
 */
class Cost
extends \DaveBaker\Core\Installer\Base
implements \DaveBaker\Core\Installer\InstallerInterface
{
    const DEFAULT_COST_NUMBER = 'MIGRATION';
    /** @var string */
    protected $installerCode = 'impresario_cost';

    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    public function install()
    {
        $pageManager = $this->app->getPageManager();

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::COST_EDIT,
            [
                "post_title" => "Edit Cost"
            ]
        );

        $this->deltaTable(
            'cost',
            "CREATE TABLE `{{tableName}}` (
              `cost_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `cost_date` datetime DEFAULT NULL,
              `cost_number` varchar(255) DEFAULT NULL,
              `sage_number` varchar(255) DEFAULT NULL,
              `value` decimal(10,4) DEFAULT NULL,
              `created_by_id` int(11) DEFAULT NULL,
              `last_edited_by_id` int(11) DEFAULT NULL,
              `supplier_id` int(11) DEFAULT NULL,
              `cost_type` varchar(20) DEFAULT NULL,
              `parent_id` int(11) DEFAULT NULL,
              `cost_invoice_type` varchar(30) DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `is_deleted` int(1) DEFAULT '0',
              PRIMARY KEY (`cost_id`),
              KEY `cost_number` (`cost_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;"
        );

        // This has been removed as migrations were run once.
        // $this->migrateCosts();
    }


    /**
     * @return \SuttonBaker\Impresario\Model\Db\Project\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getProjectCollection()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Model\Db\Project\Collection::class);
    }


    public function migrateCosts()
    {
        // Make sure the project installer has created the actual cost total column before migrating.

        /** @var \SuttonBaker\Impresario\Installer\Project $projectInstaller */
        $projectInstaller = $this->createAppObject(\SuttonBaker\Impresario\Installer\Project::class);
        $projectInstaller->install();

        $collection = $this->getProjectCollection()
            ->where('actual_cost > 0');

        $projectsWithActualCost = $collection->load();

        
        /** @var \SuttonBaker\Impresario\Model\Db\Project $project */
        foreach ($projectsWithActualCost as $project) {
            $costItem = $this->createAppObject(\SuttonBaker\Impresario\Model\Db\Cost::class);
            $costItem
                ->setCostNumber(self::DEFAULT_COST_NUMBER)
                ->setValue($project->getActualCost())
                ->setCreatedById(1)
                ->setCostType(CostDefinition::COST_TYPE_PROJECT)
                ->setParentId($project->getId())
                ->setCostDate($this->getDateHelper()->utcTimestampToDb())
                ->setCostInvoiceType(CostDefinition::COST_INVOICE_TYPE_MIGRATION_INITIAL);
            $costItem->save();
            
            // The actual cost column will no longer be used
            $project->setActualCost(0)
                ->save();
        }
    }
}
