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
            \SuttonBaker\Impresario\Definition\Page::TASK_EDIT, [
                "post_title" => "Edit Task"
            ]
        );

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::TASK_LIST, [
                "post_title" => "Tasks"
            ]
        );

        $this->deltaTable('task',
            'CREATE TABLE `{{tableName}}` (
              `task_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `created_by_id` int(11) DEFAULT NULL,
              `last_edited_by_id` int(11) DEFAULT NULL,
              `description` text,
              `notes` text,
              `created_by_id` int(11) DEFAULT NULL,
              `last_edited_by_id` int(11) DEFAULT NULL,
              `assigned_to_id` int(11) DEFAULT NULL,
              `task_type` varchar(255) DEFAULT NULL,
              `parent_id` int(11) DEFAULT NULL,
              `target_date` datetime DEFAULT NULL,
              `priority` varchar(20) DEFAULT NULL,
              `status` varchar(10) DEFAULT NULL,
              `date_completed` datetime DEFAULT NULL,
              `completed_by_id` int(11) DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              `is_deleted` int(1) DEFAULT 0,
              PRIMARY KEY (`task_id`),
              KEY `priority` (`priority`),
              KEY `status` (`status`),
              KEY `task_id` (`task_id`,`priority`),
              KEY `task_id_2` (`task_id`,`priority`,`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;'
        );
    }

}