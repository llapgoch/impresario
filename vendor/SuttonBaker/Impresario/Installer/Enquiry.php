<?php

namespace SuttonBaker\Impresario\Installer;
/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Installer\
 */
class Enquiry
    extends \DaveBaker\Core\Installer\Base
    implements \DaveBaker\Core\Installer\InstallerInterface
{
    protected $installerCode = 'impresario_enquiry';
    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    public function install()
    {
        $pageManager = $this->app->getPageManager();

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_EDIT, [
                "post_title" => "Edit Enquiry"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_LIST, [
                "post_title" => "Enquiries"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_REPORT_DOWNLOAD, [
                "post_title" => "Enquiries' Report Downloader"
            ]
        );

        $this->deltaTable('enquiry',
            'CREATE TABLE `{{tableName}}` (
              `enquiry_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `created_by_id` int(11) DEFAULT NULL,
              `last_edited_by_id` int(11) DEFAULT NULL,
              `client_reference` varchar(255) DEFAULT NULL,
              `client_id` int(11) DEFAULT NULL,
              `client_requested_by` varchar(255) DEFAULT NULL,
              `site_name` varchar(255) DEFAULT NULL,
              `date_received` datetime DEFAULT NULL,
              `status` varchar(255) DEFAULT NULL,
              `target_date` datetime DEFAULT NULL,
              `notes` text,
              `assigned_to_id` int(11) DEFAULT NULL,
              `engineer_id` int(11) DEFAULT NULL,
              `date_completed` datetime DEFAULT NULL,
              `po_number` varchar(255) DEFAULT NULL,
              `mi_number` varchar(255) DEFAULT NULL,
              `nm_mw_number` varchar(255) DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `is_deleted` int(1) DEFAULT 0,
              PRIMARY KEY (`enquiry_id`),
              KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;'
        );
    }
}