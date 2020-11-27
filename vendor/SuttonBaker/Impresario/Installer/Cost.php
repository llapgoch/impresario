<?php

namespace SuttonBaker\Impresario\Installer;
/**
 * Class InvoiceVariation
 * @package SuttonBaker\Impresario\Installer
 */
class Cost
    extends \DaveBaker\Core\Installer\Base
    implements \DaveBaker\Core\Installer\InstallerInterface
{
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
            \SuttonBaker\Impresario\Definition\Page::COST_EDIT, [
                "post_title" => "Edit Cost"
            ]
        );

        $this->deltaTable('cost',
            "CREATE TABLE `{{tableName}}` (
              `cost_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `cost_date` datetime DEFAULT NULL,
              `cost_number` varchar(255) DEFAULT NULL,
              `value` decimal(10,4) DEFAULT NULL,
              `created_by_id` int(11) DEFAULT NULL,
              `last_edited_by_id` int(11) DEFAULT NULL,
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

    }

}