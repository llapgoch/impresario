<?php

namespace SuttonBaker\Impresario\WP\Config;

class Page extends \DaveBaker\Core\WP\Config\Page
{
    public function __construct()
    {
        $this->mergeConfig([
            "defaultValues" => [
                "post_author" => "dave.baker"
            ]
        ]);
    }
}