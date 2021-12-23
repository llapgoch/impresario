<?php

namespace SuttonBaker\Impresario\Controller\Project;
use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class ListController
 * @package SuttonBaker\Impresario\Controller\Project
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';
    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_PROJECT,
        Roles::CAP_VIEW_PROJECT,
        Roles::CAP_ALL
    ];


    public function execute(){}
}