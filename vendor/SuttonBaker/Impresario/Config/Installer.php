<?php

namespace SuttonBaker\Impresario\Config;
/**
 * Class Installer
 * @package SuttonBaker\Impresario\Config
 */
class Installer extends \DaveBaker\Core\Config\Installer
{
    /**
     * Installer constructor
     */
    public function __construct()
    {
        $this->mergeConfig([
            'impresario_client' => '0.0.1',
            'impresario_enquiry' => '0.0.6'
        ]);
    }
}