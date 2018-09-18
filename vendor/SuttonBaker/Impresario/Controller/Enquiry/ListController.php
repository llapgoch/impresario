<?php

namespace SuttonBaker\Impresario\Controller\Enquiry;
use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class EnquiryListController
 * @package SuttonBaker\Impresario\Controller\Enquiry
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_ENQUIRY,
        Roles::CAP_VIEW_ENQUIRY,
        Roles::CAP_ALL
    ];

    public function execute(){}
}