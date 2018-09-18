<?php

namespace SuttonBaker\Impresario\Controller\Quote;
use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class ListController
 * @package SuttonBaker\Impresario\Controller\Quote
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_QUOTE,
        Roles::CAP_VIEW_QUOTE,
        Roles::CAP_ALL
    ];

    public function execute(){}
}