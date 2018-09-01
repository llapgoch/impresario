<?php

namespace SuttonBaker\Impresario\Config;
/**
 * Class Object
 * @package SuttonBaker\Impresario\Config
 */
class Object extends \DaveBaker\Core\Config\Object
{
    public function __construct()
    {
        $this->mergeConfig([
            '\DaveBaker\Core\Config\Installer' => [
                'definition' => '\SuttonBaker\Impresario\Config\Installer'
            ],
            '\DaveBaker\Core\Config\Page' => [
                'definition' => '\SuttonBaker\Impresario\Config\Page'
            ],
            '\DaveBaker\Core\Config\Layout' => [
                'definition' => '\SuttonBaker\Impresario\Config\Layout'
            ],
            '\DaveBaker\Core\Config\Element' => [
                'definition' => '\SuttonBaker\Impresario\Config\Element'
            ]
        ]);
    }
}