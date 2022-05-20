<?php

namespace SuttonBaker\Impresario\Installer;

use \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
use \SuttonBaker\Impresario\Definition\Invoice as InvoiceDefinition;
use SuttonBaker\Impresario\Definition\Invoice;

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

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::COST_PRINT,
            [
                "post_title" => "Print Purchase Order"
            ]
        );

        $this->deltaTable(
            'cost',
            "CREATE TABLE `{{tableName}}` (
              `cost_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `cost_date` datetime DEFAULT NULL,
              `delivery_date` datetime DEFAULT NULL,
              `status` varchar(255) DEFAULT NULL,
              `supplier_quote_number` varchar(255) DEFAULT NULL,
              `cost_number` varchar(255) DEFAULT NULL,
              `sage_number` varchar(255) DEFAULT NULL,
              `value` decimal(10,4) DEFAULT NULL,
              `po_item_total` decimal(10,4) DEFAULT NULL,
              `amount_invoiced` decimal(10,4) DEFAULT NULL,
              `invoice_amount_remaining` decimal(10,4) DEFAULT NULL,
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

        $this->deltaTable(
            'cost_po_item',
            "CREATE TABLE `{{tableName}}` (
            `po_item_id` int NOT NULL AUTO_INCREMENT,
            `cost_id` int NOT NULL,
            `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `qty` int NOT NULL,
            `unit_price` decimal(10,4) NOT NULL,
            `total` decimal(10,4) NOT NULL,
            `created_by_id` int(11) DEFAULT NULL,
            `last_edited_by_id` int(11) DEFAULT NULL,
            `created_at` datetime DEFAULT NULL,
            `updated_at` datetime DEFAULT NULL,
            `is_deleted` int(1) DEFAULT '0',
            PRIMARY KEY (`po_item_id`),
            KEY `cost_id` (`cost_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        );

        // This has been removed as migrations were run once.
        // $this->migrateCosts();


        // This uses the data migration system to stop it running multiple times
        // $this->migratePoItems();

        // $this->fixDuplicatPoItems();
    }

    protected function fixDuplicatPoItems()
    {
        $collection = $this->getCostCollection()
            ->where('is_deleted = 0');

        $collection->getSelect()->limit(5000);

        $costs = $collection->load();

        foreach ($costs as $cost) {
            $items = $this->getCostHelper()->getCostInvoiceItems($cost->getId())->load();
            $invoices = $this->getInvoiceHelper()->getInvoiceCollectionForEntity($cost->getId(), InvoiceDefinition::INVOICE_TYPE_PO_INVOICE)->load();

            // Correct
            if (count($items) === 1) {
                continue;
            }

            array_shift($items);
            array_shift($invoices);


            var_dump($cost->getId());
            // var_dump(count($items));
            // var_dump(count($invoices));exit;


            foreach ($items as $item) {
                $item->setIsDeleted(1)
                    ->save();
            }

            foreach ($invoices as $invoice) {
                $invoice->setIsDeleted(1)
                    ->save();
            }

            // exit;
        }
    }


    /**
     * @return \SuttonBaker\Impresario\Model\Db\Project\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getProjectCollection()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Model\Db\Project\Collection::class);
    }

        /**
     * @return \SuttonBaker\Impresario\Helper\Invoice
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getInvoiceHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Invoice');
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Project\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getCostCollection()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Model\Db\Cost\Collection::class);
    }

    public function migratePoItems()
    {
        $migrationCode = 'costs_to_po_items_1192001';
        $migrationHelper = $this->getMigrationHelper();


        // Ensure this runs once only
        if (!$migrationHelper->migrationHasRun($migrationCode)) {
            $migration = $migrationHelper->getMigrationByCode($migrationCode);
            $migration->setCode($migrationCode);

            // Do a batch every run. THIS SHOULD BE DONE VIA CLI, BUT PAAAAH.
            $this->migrateCostsToPoItemBatch($migration);
        }
    }


    /**
     * @return \SuttonBaker\Impresario\Helper\Cost
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getCostHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Cost');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Migration
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getMigrationHelper()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\Migration::class);
    }

    public function migrateCostsToPoItemBatch($migration)
    {
        // Use status = CLOSED to get unprocessed records as needs to run multiple times to not run out of memory
        $collection = $this->getCostCollection()
            ->where('is_deleted = 0')
            ->where('status IS NULL');

        $collection->getSelect()->limit(200);

        $costs = $collection->load();

        /** @var \SuttonBaker\Impresario\Model\Db\Cost $costItem */
        foreach ($costs as $costItem) {
            $poItem = $this->createAppObject(\SuttonBaker\Impresario\Model\Db\Cost\Item::class);

            $costItemValue = $costItem->getValue();

            $poItem->setCreatedById(1)
                ->setLastEditedById(1)
                ->setDescription('MIGRATION')
                ->setUnitPrice($costItemValue)
                ->setQty(1)
                ->setTotal($costItemValue)
                ->setCostId($costItem->getId());

            $poItem->save();

            $invoice = $this->createAppObject(\SuttonBaker\Impresario\Model\Db\Invoice::class);

            $invoice->setInvoiceType(Invoice::INVOICE_TYPE_PO_INVOICE)
                ->setInvoiceNumber($costItem->getCostNumber())
                ->setValue($costItemValue)
                ->setCreatedById(1)
                ->setLastEditedById(1)
                ->setParentId($costItem->getId());

            $invoice->save();

            $costItem->setValue(0)
                ->setStatus(CostDefinition::STATUS_CLOSED)
                ->save();

            // exit;
        }

        // Check if we're complete - if there are no un-closed (migrated items) then mark the data migration as complete
        $collection = $this->getCostCollection()
            ->where('is_deleted = 0')
            ->where('status IS NULL');

        if (count($collection->load()) == 0) {
            $migration->setHasRun(1)
                ->save();
        }
    }


    public function migrateCosts()
    {
        // NOTE: THIS IS THE VERY OLD COST MIGRATOR - NOW DEFUNCT

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
