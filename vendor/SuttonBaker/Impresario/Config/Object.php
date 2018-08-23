<?php

namespace SuttonBaker\Impresario\Config;

class Object extends \DaveBaker\Core\Config\Object
{
    public function __construct()
    {
        $this->mergeConfig([
            '\SuttonBaker\Impresario\Model\Db\Job' => [
                'definition' => '\SuttonBaker\Impresario\Model\Db\Job',
                'singleton' => false
            ],
            '\DaveBaker\Core\Config\Installer' => [
                'definition' => '\SuttonBaker\Impresario\Config\Installer',
                'singleton' => true
            ],
            '\DaveBaker\Core\Installer\Manager' => [
                'definition' => '\SuttonBaker\Impresario\Installer\Manager',
                'singleton' => true
            ],
            '\DaveBaker\Core\Config\Page' => [
                'definition' => '\SuttonBaker\Impresario\Config\Page',
                'singleton' => true
            ],
            '\DaveBaker\Core\Config\Layout' => [
                'definition' => '\SuttonBaker\Impresario\Config\Layout',
                'singleton' => true
            ],
        ]);
    }
}