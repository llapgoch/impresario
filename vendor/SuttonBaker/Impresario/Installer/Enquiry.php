<?php

namespace SuttonBaker\Impresario\Installer;
/**
 * Class Manager
 * @package SuttonBaker\Impresario\Installer\Enquiry
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

        $this->deltaTable('enquiry',
            'CREATE TABLE `{{tableName}}` (
              `enquiry_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `client_reference` varchar(255) DEFAULT NULL,
              `client_id` int(11) DEFAULT NULL,
              `site_name` varchar(255) DEFAULT NULL,
              `date_received` datetime DEFAULT NULL,
              `owner_id` int(11) DEFAULT NULL,
              `status` varchar(255) DEFAULT NULL,
              `target_date` datetime DEFAULT NULL,
              `notes` text,
              `completed_by_id` int(11) DEFAULT NULL,
              `completed_date` datetime DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `is_deleted` int(1) DEFAULT NULL,
              PRIMARY KEY (`enquiry_id`),
              KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        );
    }

}