<?php

namespace SuttonBaker\Impresario\WP\Config;

class Object extends \DaveBaker\Core\WP\Config\Object
{
    public function __construct()
    {
        $this->mergeConfig([
            '\SuttonBaker\Impresario\Model\Db\Job' => [
                'definition' => '\SuttonBaker\Impresario\Model\Db\Job',
                'singleton' => false
            ],
            '\DaveBaker\Core\WP\Config\Installer' => [
                'definition' => '\SuttonBaker\Impresario\WP\Config\Installer',
                'singleton' => true
            ],
            '\DaveBaker\Core\WP\Installer\Manager' => [
                'definition' => '\SuttonBaker\Impresario\WP\Installer\Manager',
                'singleton' => true
            ]
        ]);
    }
}