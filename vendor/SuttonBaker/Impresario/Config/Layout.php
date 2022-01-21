<?php

namespace SuttonBaker\Impresario\Config;
/**
 * Class Layout
 * @package SuttonBaker\Impresario\Config
 */
class Layout extends \DaveBaker\Core\Config\Layout
{
    public function __construct()
    {
        $this->mergeConfig([
            'templates' => [
                1 => 'impresario' . DS . 'design' . DS . 'templates'
            ]
        ]);
    }

}