<?php

namespace SuttonBaker\Impresario\Installer;
/**
 * Class InvoiceVariation
 * @package SuttonBaker\Impresario\Installer
 */
class InvoiceVariation
    extends \DaveBaker\Core\Installer\Base
    implements \DaveBaker\Core\Installer\InstallerInterface
{
    protected $installerCode = 'impresario_invoice_variation';

    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    public function install()
    {
        $pageManager = $this->app->getPageManager();

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::INVOICE_EDIT, [
                "post_title" => "Edit Invoice"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::VARIATION_EDIT, [
                "post_title" => "Edit Variation"
            ]
        );

        $this->deltaTable('invoice',
            "CREATE TABLE `{{tableName}}` (
              `invoice_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `invoice_date` datetime DEFAULT NULL,
              `invoice_number` varchar(255) DEFAULT NULL,
              `value` decimal(10,4) DEFAULT NULL,
              `created_by_id` int(11) DEFAULT NULL,
              `last_edited_by_id` int(11) DEFAULT NULL,
              `invoice_type` varchar(20) DEFAULT NULL,
              `parent_id` int(11) DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `is_deleted` int(1) DEFAULT '0',
              PRIMARY KEY (`invoice_id`),
              KEY `invoice_number` (`invoice_number`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;"
        );

        $this->deltaTable('variation',
            "CREATE TABLE `{{tableName}}` (
              `variation_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `project_id` int(11) DEFAULT NULL,
              `po_number` varchar(255) DEFAULT NULL,
              `date_approved` datetime DEFAULT NULL,
              `description` text,
              `value` decimal(10,4) DEFAULT NULL,
              `net_cost` decimal(10,4) DEFAULT NULL,
              `profit` decimal(10,4) DEFAULT NULL,
              `gp` decimal(10,4) DEFAULT NULL,
              `status` varchar(20) DEFAULT NULL,
              `created_by_id` int(11) DEFAULT NULL,
              `last_edited_by_id` int(11) DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `is_deleted` int(1) DEFAULT '0',
              PRIMARY KEY (`variation_id`),
              KEY `project_id` (`project_id`),
              KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;"
        );
    }

}