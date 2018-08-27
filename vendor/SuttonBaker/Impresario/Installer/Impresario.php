<?php

namespace SuttonBaker\Impresario\Installer;
/**
 * Class Manager
 * @package SuttonBaker\Impresario\Installer
 */
class Impresario
    extends \DaveBaker\Core\Installer\Base
    implements \DaveBaker\Core\Installer\InstallerInterface
{
    protected $installerCode = 'impresario_application';
    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    public function install()
    {

        $this->installClient();
        $this->installEnquiry();
    }

    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    protected function installClient()
    {
        $pageManager = $this->app->getPageManager();

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::CLIENT_EDIT, [
                "post_title" => "Edit Client"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::CLIENT_LIST, [
                "post_title" => "Clients"
            ]
        );

        $this->deltaTable('client',
            'CREATE TABLE `{{tableName}}` (
              `client_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `client_name` varchar(255) DEFAULT NULL,
              `address_line1` varchar(255) DEFAULT NULL,
              `address_line2` varchar(255) DEFAULT NULL,
              `address_line3` varchar(255) DEFAULT NULL,
              `postcode` varchar(255) DEFAULT NULL,
              `county` varchar(255) DEFAULT NULL,
              `country_code` varchar(4) DEFAULT NULL,
              `created_at` DATETIME DEFAULT NULL,
              `updated_at` DATETIME DEFAULT NULL,
              `sales_contact_phone` varchar(255) DEFAULT NULL,
              `sales_contact` varchar(255) DEFAULT NULL,
              `accounts_contact_phone` varchar(255) DEFAULT NULL,
              `accounts_contact` varchar(255) DEFAULT NULL,
              `is_deleted` int(1) DEFAULT 0
              PRIMARY KEY (`client_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        );
    }

    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    protected function installEnquiry()
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
              `our_reference` varchar(255) DEFAULT NULL,
              `cleint_reference` varchar(255) DEFAULT NULL,
              `client_id` int(11) DEFAULT NULL,
              `site_name` varchar(255) DEFAULT NULL,
              `date_received` datetime DEFAULT NULL,
              `owner` int(11) DEFAULT NULL,
              `status` varchar(255) DEFAULT NULL,
              `target_date` datetime DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `is_deleted` int(1) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        );
    }
}