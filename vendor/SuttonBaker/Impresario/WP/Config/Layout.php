<?php
namespace SuttonBaker\Impresario\WP\Config;

class Layout extends \DaveBaker\Core\WP\Config\Layout
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