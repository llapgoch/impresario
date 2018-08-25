<?php

namespace SuttonBaker\Impresario\Installer;
/**
 * Class Manager
 * @package SuttonBaker\Impresario\Installer
 */
class Manager
    extends \DaveBaker\Core\Installer\Manager
    implements \DaveBaker\Core\Installer\InstallerInterface
{
    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    public function install()
    {
        $pageManager = $this->app->getPageManager();

        $pageManager->createPage(
            'client_edit',
            [
                "post_title" => "Client Edit"
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
              `country_id` int(11) DEFAULT NULL,
              `sales_contact_phone` varchar(255) DEFAULT NULL,
              `sales_contact` varchar(255) DEFAULT NULL,
              `accounts_contact_phone` varchar(255) DEFAULT NULL,
              `accounts_contact` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`client_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        );
        
    }
}