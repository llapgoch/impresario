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
            'impresario_enquiry' => '0.0.98',
            'impresario_task' => '0.0.8',
            'impresario_quote' => '0.0.995',
            'impresario_project' => '0.0.37',
            'impresario_cost' => '0.0.991',
            'impresario_invoice_variation' => '0.0.10',
            'impresario_general' => '0.0.993',
            'impresario_archive' => '0.0.4',
            'impresario_supplier' => '0.0.1'
        ]);
    }
}