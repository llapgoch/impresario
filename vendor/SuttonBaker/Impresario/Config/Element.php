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
                'tile-white' => 'transparent-white',
                'tile-black' => 'transparent-black',
                'form' => 'form-horizontal',
                'label' => 'col-sm-4 control-label',
                'form-group' => 'form-group'
            ]
        ]);

    }
}