<?php

namespace SuttonBaker\Impresario\Controller\Supplier;
use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class SupplierListController
 * @package SuttonBaker\Impresario\Controller\Supplier
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_SUPPLIER,
        Roles::CAP_VIEW_SUPPLIER,
        Roles::CAP_ALL
    ];


    public function execute(){}
}