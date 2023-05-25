<?php

namespace SuttonBaker\Impresario\Installer;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Installer\
 */
class Quote
    extends \DaveBaker\Core\Installer\Base
    implements \DaveBaker\Core\Installer\InstallerInterface
{
    protected $installerCode = 'impresario_quote';

    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    public function install()
    {
        $pageManager = $this->app->getPageManager();

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT, [
                "post_title" => "Edit Quote"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::QUOTE_LIST, [
                "post_title" => "Quote Register"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::QUOTE_REPORT_DOWNLOAD, [
                "post_title" => "Quote Report Downloader"
            ]
        );

        $this->deltaTable('quote',
            "CREATE TABLE `{{tableName}}` (
                `quote_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `date_received` datetime DEFAULT NULL,
              `created_by_id` int(11) DEFAULT NULL,
              `last_edited_by_id` int(11) DEFAULT NULL,
              `client_id` int(11) DEFAULT NULL,
              `enquiry_id` int(11) DEFAULT NULL,
              `type_id` int(11) DEFAULT NULL,
              `parent_id` int(11) DEFAULT NULL,
              `project_name` varchar(255) DEFAULT NULL,
              `site_name` varchar(255) DEFAULT NULL,
              `client_requested_by` varchar(255) DEFAULT NULL,
              `client_reference` varchar(255) DEFAULT NULL,
              `date_required` datetime DEFAULT NULL,
              `estimator_id` int(11) DEFAULT NULL,
              `net_cost` decimal(10,4) DEFAULT NULL,
              `net_sell` decimal(10,4) DEFAULT NULL,
              `profit` decimal(10,4) DEFAULT 0,
              `gp` decimal(10,4) DEFAULT 0,
              `date_returned` datetime DEFAULT NULL,
              `date_completed` datetime DEFAULT NULL,
              `completed_by_id` int(11) DEFAULT NULL,   
              `po_number` varchar(255) DEFAULT NULL,
              `mi_number` varchar(255) DEFAULT NULL,
              `nm_mw_number` varchar(255) DEFAULT NULL,
              `revision_number` int(11) DEFAULT NULL,
              `status` varchar(20) DEFAULT NULL,
              `tender_status` varchar(20) DEFAULT NULL,
              `is_master` int(1) DEFAULT 0,
              `comments` text,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `is_deleted` int(1) DEFAULT '0',
              PRIMARY KEY (`quote_id`),
              KEY `status` (`status`),
              KEY `tender_status` (`tender_status`),
              KEY `status_tender_status` (`status`, `tender_status`),
              KEY `enquiry_id` (`enquiry_id`),
              KEY `type_id` (`type_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;"
        );

        $this->deltaTable('quote_project_type',
            "CREATE TABLE `{{tableName}}` (
            `type_id` int NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `code` varchar(30) DEFAULT NULL,
            `sort_order` int NULL,
            PRIMARY KEY (`type_id`),
            KEY `code` (`code`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;"
        );
    }
}