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
        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            /** @var \SuttonBaker\Impresario\Model\Db\Enquiry $entityInstance */
            $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Enquiry')->load($entityId);
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


        if($entityId) {
            $urlParams = [];

            if ($quoteEntity->getId()) {
                $urlParams['quote_id'] = $quoteEntity->getId();
            } else {
                $urlParams['enquiry_id'] = $entityId;
            }

            $mainTile->addChildBlock(
                $quoteLink = $mainTile->createBlock(
                    '\DaveBaker\Core\Block\Html\ButtonAnchor',
                    'create.quote.link',
                    'header_elements'
                )
                    ->setTagText($quoteEntity->getId() ? 'View Quote' : 'Create Quote')
                    ->addAttribute(
                        ['href' => $this->getRequest()->getUrlHelper()->getPageUrl(
                            \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT,
                            $urlParams,
                            true
                        )]
                    )
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
                )
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