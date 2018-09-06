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
    const DELETE_ACTION = 'delete';

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_ENQUIRY,
        Roles::CAP_VIEW_ENQUIRY,
        Roles::CAP_ALL
    ];

    /**
     * @return \SuttonBaker\Impresario\Controller\Base|void
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $action = $this->getRequest()->getPostParam('action');

        // Perform enquiry deletes
        if(($enquiryId = $this->getRequest()->getPostParam('enquiry_id')) && $action == self::DELETE_ACTION){
            /** @var \SuttonBaker\Impresario\Model\Db\Enquiry\ $enquiry */
            $enquiry = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Enquiry')->load($enquiryId);

            if(!$enquiry->getId()){
                return;
            }

            $this->getEnquiryHelper()->deleteEnquiry($enquiry);

            $this->addMessage('The enquiry has been removed', Messages::SUCCESS);
            $this->getResponse()->redirectReferer();
        }
    }

    public function execute()
    {
        // TODO: Implement execute() method.
    }
}