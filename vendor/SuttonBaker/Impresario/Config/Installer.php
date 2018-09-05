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
            'impresario_client' => '0.0.6',
            'impresario_enquiry' => '0.0.93',
            'impresario_task' => '0.0.7',
            'impresario_quote' => '0.0.5',
            'impresario_project' => '0.0.3'
        ]);
    }
}