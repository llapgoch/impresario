<?php

namespace SuttonBaker\Impresario\Installer;

/**
 * Class Supplier
 * @package SuttonBaker\Impresario\Installer\
 */
class Supplier
    extends \DaveBaker\Core\Installer\Base
    implements \DaveBaker\Core\Installer\InstallerInterface
{
    protected $installerCode = 'impresario_supplier';
    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    public function install()
    {

        $pageManager = $this->app->getPageManager();

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::SUPPLIER_EDIT, [
                "post_title" => "Edit Supplier"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::SUPPLIER_LIST, [
                "post_title" => "Suppliers"
            ]
        );

        $this->deltaTable('supplier',
            'CREATE TABLE `{{tableName}}` (
              `supplier_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `created_by_id` int(11) DEFAULT NULL,
              `last_edited_by_id` int(11) DEFAULT NULL,
              `supplier_name` varchar(255) DEFAULT NULL,
              `address_line1` varchar(255) DEFAULT NULL,
              `address_line2` varchar(255) DEFAULT NULL,
              `address_line3` varchar(255) DEFAULT NULL,
              `postcode` varchar(255) DEFAULT NULL,
              `county` varchar(255) DEFAULT NULL,
              `country_code` varchar(4) DEFAULT NULL,
              `created_at` DATETIME DEFAULT NULL,
              `updated_at` DATETIME DEFAULT NULL,
              `supplier_contact_phone` varchar(255) DEFAULT NULL,
              `supplier_contact` varchar(255) DEFAULT NULL,
              `accounts_contact_phone` varchar(255) DEFAULT NULL,
              `accounts_contact` varchar(255) DEFAULT NULL,
              `is_deleted` int(1) DEFAULT 0,
              PRIMARY KEY (`supplier_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;'
        );
    }


}