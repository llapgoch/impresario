<?php

namespace SuttonBaker\Impresario\WP\Controller;

class DefaultController
    extends \DaveBaker\Core\WP\Controller\Base
    implements \DaveBaker\Core\WP\Controller\ControllerInterface
{

    public function execute()
    {
        var_dump("executing default controller");
    }
}