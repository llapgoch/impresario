<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Layout
 */
class Enquiry extends Base
{
    protected $blockPrefix = 'enquiry';

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function enquiryEditHandle()
    {
        $entityId = $this->getRequest()->getParam('enquiry_id');


        $this->addBlock(
            /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setShortcode('body_content')
                ->setHeading('Edit')
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Enquiry\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('enquiry_edit_form')->setFormAction($this->getUrlHelper()->getCurrentUrl())

        );




//        $this->addBlock(
//            $formContainer = $this->createBlock('\SuttonBaker\Impresario\Block\Form\Container', 'enquiry.edit.form.container')
//                ->setShortcode('body_content')
//        );
//
//        $formContainer->addChildBlock(
//            $this->createBlock(
//                '\DaveBaker\Core\Block\Html\Heading',
//                "enquiry.form.edit.heading")
//                ->setHeading($entityId ? 'Edit Enquiry' : 'Create New Enquiry')
//                ->setTemplate('core/main-header.phtml')
//        );
//
//        $formContainer->addChildBlock($this->getBlockManager()->getMessagesBlock());
//

    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
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