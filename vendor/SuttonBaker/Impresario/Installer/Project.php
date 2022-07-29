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

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::PROJECT_REPORT_DOWNLOAD, [
                "post_title" => "Project Report Downloader"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::PROJECT_SALES_INVOICE_DOWNLOAD, [
                "post_title" => "Project Sales Invoice Downloader"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::PROJECT_COST_INVOICE_DOWNLOAD, [
                "post_title" => "Project Cost Invoice Downloader"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::PROJECT_VARIATION_INVOICE_DOWNLOAD, [
                "post_title" => "Project Variation Downloader"
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
              `client_project_manager` varchar(255) DEFAULT NULL,
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
              `total_actual_cost` decimal(10,4) DEFAULT NULL,
              `profit` decimal(10,4) DEFAULT NULL,
              `amount_invoiced` decimal(10,4) DEFAULT NULL,
              `invoice_amount_remaining` decimal(10,4) DEFAULT NULL,
              `gp` decimal(10,4) DEFAULT NULL,
              `actual_profit` decimal(10,4) DEFAULT NULL,
              `actual_margin` decimal(10,4) DEFAULT NULL,
              `project_start_date` datetime DEFAULT NULL,
              `project_end_date` datetime DEFAULT NULL,
              `po_number` varchar(255) DEFAULT NULL,
              `mi_number` varchar(255) DEFAULT NULL,
              `po_mi_number` varchar(255) DEFAULT NULL,
              `nm_mw_number` varchar(255) DEFAULT NULL,
              `status` varchar(20) DEFAULT NULL,
              `comments` text,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `checklist_plant_off_hired` int(1) DEFAULT NULL,
              `checklist_cost_invoice_received_logged` int(1) DEFAULT NULL,
              `checklist_rams_qhse_filing` int(1) DEFAULT NULL,
              `checklist_customer_satisfaction_survey_logged` int(1) DEFAULT NULL,
              `checklist_completion_photos_logged` int(1) DEFAULT NULL,
              `checklist_warranty_guarantee_certificate_filed` int(1) DEFAULT NULL,
              `checklist_client_advised_operational_maintenance` int(1) DEFAULT NULL,
              `checklist_client_crm_updated` int(1) DEFAULT NULL,
              `project_manager_closing_feedback` text,
              `is_deleted` int(1) DEFAULT '0',
              PRIMARY KEY (`project_id`),
              KEY `status` (`status`),
              KEY `quote_id` (`quote_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;"
        );
    }

}