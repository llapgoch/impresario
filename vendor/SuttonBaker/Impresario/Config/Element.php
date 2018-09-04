<?php

namespace SuttonBaker\Impresario\Config;
/**
 * Class Element
 * @package SuttonBaker\Impresario\Config
 */
class Element extends \DaveBaker\Core\Config\Element
{
    /**
     * Installer constructor
     */
    public function __construct()
    {
        $this->mergeConfig([
            'elementClasses' => [
                'input' => 'form-control',
                'input-submit' => 'btn btn-primary',
                'select' => 'form-control',
                'textarea' => 'form-control',
                'button' => 'btn btn-primary',
                'tile' => 'tile',
                'tile-white' => 'color transparent-white',
                'tile-black' => 'color transparent-black',
                'form' => '',
                'label' => 'control-label',
                'form-group' => 'form-group',
                'form-row' => 'form-row',
                'button-anchor' => 'btn btn-primary',
                'form-error-message' => 'alert alert-danger',
                'table' => 'table',
                'table-status-rows' => 'table-status-rows'
            ],
            'elementAttributes' => [
                'textarea' => ['rows' => 8]
            ]
        ]);

    }
}