<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Layout
 */
class Quote extends Base
{
    const ID_KEY = 'quote_id';

    /** @var string  */
    protected $blockPrefix = 'quote';

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function quoteEditHandle()
    {
        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            /** @var \SuttonBaker\Impresario\Model\Db\Quote $entityInstance */
            $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Quote')->load($entityId);
            $editMode = true;
        }


        $this->addBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Heading',
                "{$this->getBlockPrefix()}.form.edit.heading")
                ->setHeading('Quote Register')
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
                ->setHeading($entityId ? '<strong>Update</strong> Quote' : '<strong>Create a </strong>Quote')
                ->setShortcode('body_content')
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Quote\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('enquiry_edit_form')

        );


        // TODO: Change this for enquiry
//        if($entityId) {
//            $urlParams = [];
//
//            $mainTile->addChildBlock(
//                $quoteLink = $mainTile->createBlock(
//                    '\DaveBaker\Core\Block\Html\ButtonAnchor',
//                    'create.quote.link',
//                    'header_elements'
//                )
//                    ->setTagText($quoteEntity->getId() ? 'View Quote' : 'Create Quote')
//                    ->addAttribute(
//                        ['href' => $this->getRequest()->getUrlHelper()->getPageUrl(
//                            \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT,
//                            $urlParams,
//                            true
//                        )]
//                    )
//            );
//        }
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     */
    public function quoteListHandle()
    {
        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Quote\QuoteList',
                'task.list'
            )->setShortcode('body_content')
        );
    }
}