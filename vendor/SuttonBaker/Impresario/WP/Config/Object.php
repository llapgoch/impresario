<?php

namespace SuttonBaker\Impresario\WP\Config;

class Object extends \DaveBaker\Core\WP\Config\Object
{
    public function __construct()
    {
        $this->mergeConfig([
            '\SuttonBaker\Impresario\Model\Db\Job' => [
                'definition' => '\SuttonBaker\Impresario\Model\Db\Job',
                'singleton' => true
            ]
        ]);
    }
}