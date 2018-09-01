<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Layout
 */
class Enquiry extends \DaveBaker\Core\Layout\Base
{

    /**
     * @throws \DaveBaker\Core\App\Exception
     */
    public function enquiryEditHandle()
    {
        $entityId = $this->getRequest()->getParam('enquiry_id');
        $this->addBlock(
            $formContainer = $this->createBlock('\SuttonBaker\Impresario\Block\Form\Container', 'enquiry.edit.form.container')
                ->setShortcode('body_content')
        );

        $formContainer->addChildBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Heading',
                "enquiry.form.edit.heading")
                ->setHeading($entityId ? 'Edit Enquiry' : 'Create New Enquiry')
                ->setTemplate('core/main-header.phtml')
        );

        $formContainer->addChildBlock($this->getBlockManager()->getMessagesBlock());

        $formContainer->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Enquiry\Form\Edit',
                'enquiry.form.edit'
            )->setElementName('enquiry_edit_form')->setShortcode('body_content')->setFormAction("")

        );
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     */
    public function enquiryListHandle()
    {
        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Enquiry\EnquiryList',
                'enquiry.list'
            )->setShortcode('body_content')
        );
    }
}