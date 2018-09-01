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
                'select' => 'form-control',
                'textarea' => 'form-control',
                'button' => 'form-control',
                'tile-white' => 'transparent-white',
                'tile-black' => 'transparent-black',
                'form' => 'form-horizontal'
            ]
        ]);

    }
}