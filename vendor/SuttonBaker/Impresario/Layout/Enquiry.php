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
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Heading',
                "{$this->getBlockPrefix()}.form.edit.heading")
                ->setHeading('Enquiries')
                ->setTemplate('core/main-header.phtml')
                ->setShortcode('body_content')
        );

        $this->addBlock(
            $this->getBlockManager()->getMessagesBlock()->setShortcode('body_content')
        );


        $this->addBlock(
            /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading($entityId ? '<strong>Update</strong> Enquiry' : '<strong>Create an </strong>Enquiry')
                ->setShortcode('body_content')
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Enquiry\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('enquiry_edit_form')

        );




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