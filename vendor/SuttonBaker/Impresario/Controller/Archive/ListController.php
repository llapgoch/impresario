<?php

namespace SuttonBaker\Impresario\Controller\Archive;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class ListController
 * @package SuttonBaker\Impresario\Controller\Archive
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';
    /** @var array  */
    protected $capabilities = [
        Roles::CAP_VIEW_ARCHIVE,
        Roles::CAP_ALL
    ];


    public function execute(){

    }
}