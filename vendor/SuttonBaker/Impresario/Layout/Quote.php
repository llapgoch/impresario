<?php

namespace SuttonBaker\Impresario\Layout;

use SuttonBaker\Impresario\Definition\Page as PageDefinition;
use SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;

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

        if ($entityId = $this->getRequest()->getParam(self::ID_KEY)) {
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
                "{$this->getBlockPrefix()}.tile.main"
            )
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
                "{$this->getBlockPrefix()}.tile.main"
            )
                ->setHeading("<strong>Quote</strong> Register")
                ->setShortcode('body_content')
                ->setTileBodyClass('nopadding table-responsive')
        );

        $mainTile->addChildBlock(
            $buttonContainer = $mainTile->createBlock(
                \DaveBaker\Core\Block\Block::class,
                "{$this->getBlockPrefix()}.button.container",
                'header_elements'
            )
        );


        $buttonContainer->addChildBlock(
            $buttonContainer->createBlock(
                '\DaveBaker\Core\Block\Html\ButtonAnchor',
                "report.{$this->getBlockPrefix()}.download.link"
            )
                ->setTagText('<span class="fa fa-download" aria-hidden="true"></span>')
                ->addAttribute(
                    ['href' => $this->getRequest()->getUrlHelper()->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::QUOTE_REPORT_DOWNLOAD
                    )]
                )->setCapabilities($this->getQuoteHelper()->getViewCapabilities())
        );


        $mainTile->addChildBlock(
            $mainTile->createBlock(
                '\SuttonBaker\Impresario\Block\Quote\QuoteList',
                "{$this->getBlockPrefix()}.list",
                'content'
            )
        );

        $this->createFilterSet($mainTile);
    }

    /**
     * @param string $location
     * @return $this
     */
    public function createFilterSet(
        $location
    ) {
        /** @var \SuttonBaker\Impresario\Block\Form\Filter\Set $filterSet */
        $location->addChildBlock(
            $filterSet = $location->createBlock(
                \SuttonBaker\Impresario\Block\Form\Filter\Set::class,
                "{$this->getBlockPrefix()}.filter.set",
                'controls'
            )->setCapabilities($this->getEnquiryHelper()->getViewCapabilities())
                ->setSetName('quote_filters')
                ->addClass('js-quote-filters')
                ->addJsDataItems([
                    'tableUpdaterSelector' => '.js-quote-table'
                ])
        );

        // Clients
        $clients = $this->createCollectionSelectConnector()
            ->configure(
                $this->getClientHelper()->getClientCollection(),
                'client_id',
                'client_name'
            )->getElementData();

        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Select::class)
                ->setLabelName('Client')
                ->setFormName('client_id')
                ->setSelectOptions($clients)
        );

        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Text::class)
                ->setLabelName('Site Name')
                ->setFormName('site_name')
        );

        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Text::class)
                ->setLabelName('Project')
                ->setFormName('project_name')
        );

        $assignedToUsers = $this->createCollectionSelectConnector()
            ->configure(
                $this->getApp()->getHelper('User')->getUserCollection(),
                'ID',
                'display_name'
            )->getElementData();

        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Select::class)
                ->setLabelName('Creator')
                ->setFormName('created_by_id')
                ->setSelectOptions($assignedToUsers)
        );

        if ($estimators = $this->getRoleHelper()->getEstimators()) {
            $estimators = $this->createCollectionSelectConnector()
                ->configure(
                    $estimators,
                    'ID',
                    'display_name'
                )->getElementData();

            $filterSet->addFilter(
                $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Select::class)
                    ->setLabelName('Estimator')
                    ->setFormName('estimator_id')
                    ->setSelectOptions($estimators)
            );
        }

        $statuses = $this->createArraySelectConnector()->configure(QuoteDefinition::getStatuses())->getElementData();
        $filterSet->addFilter(
            $status = $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Select::class)
                ->setLabelName('Status')
                ->setFormName('status')
                ->setSelectOptions($statuses)
        );

        $tenderStatuses = $this->createArraySelectConnector()->configure(QuoteDefinition::getTenderStatuses())->getElementData();
        $filterSet->addFilter(
            $status = $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Select::class)
                ->setLabelName('Tender Status')
                ->setFormName('tender_status')
                ->setSelectOptions($tenderStatuses)
        );


        $filterSet->addFilter(
            /** @var \SuttonBaker\Impresario\Block\Form\Filter\DateRange $range */
            $range = $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\DateRange::class)
            ->setLabelName('Required By')
            ->setFormName('date_required')
        );

        $range->getMainElement()->addAttribute(['data-date-settings' => json_encode(
            ['maxDate' => "+5Y"]
        )]);


        $range->getToElement()->addAttribute(['data-date-settings' => json_encode(
            ['maxDate' => "+5Y"]
        )]);

        return $this;
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
