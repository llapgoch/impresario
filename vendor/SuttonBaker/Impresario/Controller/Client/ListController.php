<?php

namespace SuttonBaker\Impresario\Controller\Client;
use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class ClientListController
 * @package SuttonBaker\Impresario\Controller\Client
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_CLIENT,
        Roles::CAP_VIEW_CLIENT,
        Roles::CAP_ALL
    ];


    public function execute(){}
}