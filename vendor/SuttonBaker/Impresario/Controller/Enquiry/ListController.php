<?php

namespace SuttonBaker\Impresario\Controller\Enquiry;
use DaveBaker\Core\Definitions\Messages;

/**
 * Class EnquiryListController
 * @package SuttonBaker\Impresario\Controller\Enquiry
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';

    /**
     * @return \DaveBaker\Core\Controller\Base|void
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

            $enquiry->setIsDeleted(1)->save();
            $this->addMessage('The enquiry has been removed', Messages::SUCCESS);
            $this->getResponse()->redirectReferer();
        }
    }

    public function execute()
    {
        // TODO: Implement execute() method.
    }
}