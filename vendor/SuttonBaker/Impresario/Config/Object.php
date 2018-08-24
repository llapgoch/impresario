<?php

namespace SuttonBaker\Impresario\Config;

class Object extends \DaveBaker\Core\Config\Object
{
    public function __construct()
    {
        $this->mergeConfig([
            '\DaveBaker\Core\Config\Installer' => [
                'definition' => '\SuttonBaker\Impresario\Config\Installer'
            ],
            '\DaveBaker\Core\Installer\Manager' => [
                'definition' => '\SuttonBaker\Impresario\Installer\Manager'
            ],
            '\DaveBaker\Core\Config\Page' => [
                'definition' => '\SuttonBaker\Impresario\Config\Page'
            ],
            '\DaveBaker\Core\Config\Layout' => [
                'definition' => '\SuttonBaker\Impresario\Config\Layout'
            ]
        ]);
    }
}