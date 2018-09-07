<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Layout
 */
class Enquiry extends Base
{
    const ID_KEY = 'enquiry_id';

    /** @var string  */
    protected $blockPrefix = 'enquiry';
    protected $headingName = 'Enquiries';
    protected $icon = 'fa-thumb-tack';

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function enquiryEditHandle()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Enquiry */
        if(!($entityInstance = $this->getApp()->getRegistry()->get('model_instance'))){
            return;
        }

        $quoteEntity = null;

        if($entityId = $entityInstance->getId()){
            $editMode = true;
            $quoteEntity = $entityInstance->getQuoteEntity();
        }

        $this->addHeading();
        $this->addMessages();

        $this->addBlock(
            /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading(
                    $this->getEnquiryHelper()->currentUserCanEdit()
                        ? '<strong>Update</strong> Enquiry' : "<strong>View</strong> Enquiry")
                ->setShortcode('body_content')
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Enquiry\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('enquiry_edit_form')

        );


        if($quoteEntity && $quoteEntity->getId()) {
            $mainTile->addChildBlock(
                $quoteLink = $mainTile->createBlock(
                    '\DaveBaker\Core\Block\Html\ButtonAnchor',
                    'view.quote.link',
                    'header_elements'
                )
                    ->setTagText('View Quote')
                    ->addAttribute(
                        ['href' => $this->getRequest()->getUrlHelper()->getPageUrl(
                            \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT,
                            ['quote_id' => $quoteEntity->getId()],
                            true
                        )]
                    )->setCapabilities($this->getQuoteHelper()->getEditCapabilities())
            );
        }

    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function enquiryListHandle()
    {

      $this->addHeading()->addMessages();

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading("Enquiry <strong>List</strong>")
                ->setShortcode('body_content')
                ->setTileBodyClass('nopadding')
        );

        $mainTile->addChildBlock(
            $createLink = $mainTile->createBlock(
                '\DaveBaker\Core\Block\Html\ButtonAnchor',
                'create.enquiry.link',
                'header_elements'
            )
                ->setTagText('Create an Enquiry')
                ->addAttribute(
                    ['href' => $this->getRequest()->getUrlHelper()->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::ENQUIRY_EDIT
                    )]
                )->setCapabilities($this->getEnquiryHelper()->getEditCapabilities())
        );

        $mainTile->addChildBlock(
            $mainTile->createBlock(
                '\SuttonBaker\Impresario\Block\Enquiry\EnquiryList',
                'enquiry.list',
                'content'
            )
        );
    }
}