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
            'impresario_client' => '0.0.7',
            'impresario_enquiry' => '0.0.95',
            'impresario_task' => '0.0.8',
            'impresario_quote' => '0.0.95',
            'impresario_project' => '0.0.12',
            'impresario_invoice_variation' => '0.0.10',
            'impresario_general' => '0.0.99',
            'impresario_archive' => '0.0.2'
        ]);
    }
}