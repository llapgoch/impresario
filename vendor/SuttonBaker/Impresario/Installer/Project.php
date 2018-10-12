<?php

namespace SuttonBaker\Impresario\Installer;
/**
 * Class Project
 * @package SuttonBaker\Impresario\Installer\
 */
class Project
    extends \DaveBaker\Core\Installer\Base
    implements \DaveBaker\Core\Installer\InstallerInterface
{
    protected $installerCode = 'impresario_project';

    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    public function install()
    {
        $pageManager = $this->app->getPageManager();

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::PROJECT_LIST, [
                "post_title" => "Projects"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::PROJECT_EDIT, [
                "post_title" => "Edit Project"
            ]
        );

        $this->deltaTable('project',
            "CREATE TABLE `{{tableName}}` (
              `project_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `date_received` datetime DEFAULT NULL,
              `created_by_id` int(11) DEFAULT NULL,
              `last_edited_by_id` int(11) DEFAULT NULL,
              `client_id` int(11) DEFAULT NULL,
              `quote_id` int(11) DEFAULT NULL,
              `project_name` varchar(255) DEFAULT NULL,
              `site_name` varchar(255) DEFAULT NULL,
              `client_requested_by` varchar(255) DEFAULT NULL,
              `client_reference` varchar(255) DEFAULT NULL,
              `date_required` datetime DEFAULT NULL,
              `project_manager_id` int(11) DEFAULT NULL,
              `assigned_foreman_id` int(11) DEFAULT NULL,
              `net_cost` decimal(10,4) DEFAULT NULL,
              `net_sell` decimal(10,4) DEFAULT NULL,
              `total_net_cost` decimal(10,4) DEFAULT NULL,
              `total_net_sell` decimal(10,4) DEFAULT NULL,
              `actual_cost` decimal(10,4) DEFAULT NULL,
              `profit` decimal(10,4) DEFAULT NULL,
              `amount_invoiced` decimal(10,4) DEFAULT NULL,
              `invoice_amount_remaining` decimal(10,4) DEFAULT NULL,
              `gp` decimal(10,4) DEFAULT NULL,
              `project_start_date` datetime DEFAULT NULL,
              `project_end_date` datetime DEFAULT NULL,
              `po_number` varchar(255) DEFAULT NULL,
              `mi_number` varchar(255) DEFAULT NULL,
              `nm_mw_number` varchar(255) DEFAULT NULL,
              `status` varchar(20) DEFAULT NULL,
              `comments` text,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `is_deleted` int(1) DEFAULT '0',
              PRIMARY KEY (`project_id`),
              KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_bin;"
        );
    }

}