<?php

namespace SuttonBaker\Impresario\WP\Installer;

class Manager extends \DaveBaker\Core\WP\Installer\Manager
{
    protected function install()
    {
        $pageManager = $this->app->getPageManager();

        $pageManager->createPage(
            'job_list',
            [
                "post_title" => "Job List"
            ]
        );
    }
}