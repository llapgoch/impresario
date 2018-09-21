<?php

namespace SuttonBaker\Impresario\Installer;
/**
 * Class Archive
 * @package SuttonBaker\Impresario\Installer\
 */
class Archive
    extends \DaveBaker\Core\Installer\Base
    implements \DaveBaker\Core\Installer\InstallerInterface
{
    protected $installerCode = 'impresario_archive';
    /**
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Core\Page\Exception
     */
    public function install()
    {
        $pageManager = $this->app->getPageManager();

        $pageManager->createPage(
            \SuttonBaker\Impresario\Definition\Page::ARCHIVE_LIST, [
                "post_title" => "Archive"
            ]
        );
    }
}