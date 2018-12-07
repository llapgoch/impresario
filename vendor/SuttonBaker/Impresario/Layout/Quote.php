<?php

namespace SuttonBaker\Impresario\Layout;

use SuttonBaker\Impresario\Definition\Page as PageDefinition;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Layout
 */
class Quote extends Base
{
    const ID_KEY = 'quote_id';

    /** @var string  */
    protected $blockPrefix = 'quote';
    /** @var string  */
    protected $headingName = 'Quotes';
    protected $icon = \SuttonBaker\Impresario\Definition\Quote::ICON;

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function quoteEditHandle()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Quote $entityInstance */
        $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Quote');

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            /** @var \SuttonBaker\Impresario\Model\Db\Quote $entityInstance */
            $entityInstance->load($entityId);
        }

        $this->addHeading()->addMessages();

        $quoteRevision = $this->getQuoteHelper()->getRevisionLetter($entityInstance->getRevisionNumber());
        $masterText = $entityInstance->getIsMaster() ? '<i class="fa fa fa-star"></i> ' : '';

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading($masterText . $this->getQuoteHelper()->getActionVerb($entityInstance) . " Quote (Revision $quoteRevision)")
                ->setShortcode('body_content')
                ->addChildBlock($this->getQuoteHelper()->getTabBarForQuote($entityInstance))
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Quote\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('enquiry_edit_form')

        );

    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function quoteListHandle()
    {
        $this->addHeading()->addMessages();

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading("<strong>Quote</strong> Register")
                ->setShortcode('body_content')
                ->setTileBodyClass('nopadding table-responsive')
        );


        $mainTile->addChildBlock(
            $mainTile->createBlock(
                '\SuttonBaker\Impresario\Block\Quote\QuoteList',
                "{$this->getBlockPrefix()}.list",
                'content'
            )
        );
    }

    public function indexHandle()
    {
        $openQuotes = count($this->getQuoteHelper()->getOpenQuotes()->load());
        $totalQuotes = count($this->getQuoteHelper()->getMasterQuotes()->load());

        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\FlipCard',
                'quotes.flip.card'
            )->setShortcode('body_content')
                ->setTemplate('core/flip-card.phtml')
                ->setIcon(\SuttonBaker\Impresario\Definition\Quote::ICON)
                ->setHeading('Open Quotes')
                ->setNumber($openQuotes)
                ->setColour('greensea')
                ->setProgressPercentage($this->getQuoteHelper()->getPercentage($openQuotes, $totalQuotes))
                ->setProgressHeading("{$openQuotes} open out of {$totalQuotes} total quotes")
                ->setBackLink($this->getUrlHelper()->getPageUrl(PageDefinition::QUOTE_LIST))
                ->setBackText('View Quotes')
                ->setCapabilities($this->getQuoteHelper()->getViewCapabilities())
        );
    }
}