<?php

namespace SuttonBaker\Impresario\Config;

class Page extends \DaveBaker\Core\Config\Page
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