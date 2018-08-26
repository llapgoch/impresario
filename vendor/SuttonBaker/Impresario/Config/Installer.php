<?php

namespace SuttonBaker\Impresario\Config;
/**
 * Class Installer
 * @package SuttonBaker\Impresario\Config
 */
class Installer extends \DaveBaker\Core\Config\Installer
{
    public function __construct()
    {
        $this->mergeConfig([
            'impresario_application' => '0.0.2'
        ]);
    }
}