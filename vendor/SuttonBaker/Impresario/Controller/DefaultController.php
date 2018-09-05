<?php

namespace SuttonBaker\Impresario\Controller;
/**
 * Class DefaultController
 * @package SuttonBaker\Impresario\Controller
 */
class DefaultController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    // Add this to the default handle so all pages require a user to be logged in
    protected $requiresLogin = true;

    public function execute()
    {

    }
}