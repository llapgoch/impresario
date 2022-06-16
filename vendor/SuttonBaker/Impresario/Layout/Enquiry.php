<?php

namespace SuttonBaker\Impresario\Layout;

use SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;

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
    protected $icon = \SuttonBaker\Impresario\Definition\Enquiry::ICON;

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function enquiryEditHandle()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Enquiry */
        if (!($entityInstance = $this->getApp()->getRegistry()->get('model_instance'))) {
            return;
        }

        $this->addHeading();
        $this->addMessages();

        $this->addBlock(
            /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main"
            )
                ->setHeading($this->getEnquiryHelper()->getActionVerb($entityInstance) . " <strong>Enquiry</strong>")
                ->setShortcode('body_content')
                ->addChildBlock($this->getEnquiryHelper()->getTabBarForEnquiry($entityInstance))
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
                "{$this->getBlockPrefix()}.tile.main"
            )
                ->setHeading("<strong>Enquiry</strong> List")
                ->setShortcode('body_content')
                ->setTileBodyClass('nopadding table-responsive')
        );

        $mainTile->addChildBlock(
            $buttonContainer = $mainTile->createBlock(
                \DaveBaker\Core\Block\Block::class,
                'enquiry.button.container',
                'header_elements'
            )
        );

        $buttonContainer->addChildBlock(
            $buttonContainer->createBlock(
                '\DaveBaker\Core\Block\Html\ButtonAnchor',
                'create.enquiry.link'
            )
                ->setTagText('Create a New Enquiry')
                ->addAttribute(
                    ['href' => $this->getRequest()->getUrlHelper()->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::ENQUIRY_EDIT
                    )]
                )->setCapabilities($this->getEnquiryHelper()->getEditCapabilities())
        );



        $buttonContainer->addChildBlock(
            $buttonContainer->createBlock(
                '\DaveBaker\Core\Block\Html\ButtonAnchor',
                'report.enquiry.download.link'
            )
                ->setTagText('<span class="fa fa-download" aria-hidden="true"></span>')
                ->addAttribute(
                    ['href' => $this->getRequest()->getUrlHelper()->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::ENQUIRY_REPORT_DOWNLOAD
                    )]
                )->setCapabilities($this->getEnquiryHelper()->getViewCapabilities())
        );

        $mainTile->addChildBlock(
            $mainTile->createBlock(
                \SuttonBaker\Impresario\Block\Enquiry\EnquiryList::class,
                'enquiry.list',
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
                'enquiry.filter.set',
                'controls'
            )->setCapabilities($this->getEnquiryHelper()->getViewCapabilities())
                ->setSetName('enquiry_filters')
                ->addClass('js-enquiry-filters')
                ->addJsDataItems([
                    'tableUpdaterSelector' => '.js-enquiry-table'
                ])
        );

        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Text::class)
                ->setLabelName('ID')
                ->setFormName('enquiry_id')
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
                ->setLabelName('Client Ref')
                ->setFormName('client_reference')
        );


        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Text::class)
                ->setLabelName('MI Number')
                ->setFormName('mi_number')
        );

        /** @var \SuttonBaker\Impresario\Block\Form\Filter\Select $status */
        $filterSet->addFilter(
            $status = $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Select::class)
                ->setLabelName('Status')
                ->setFormName('status')
        );

        $statuses = $this->createArraySelectConnector()->configure(EnquiryDefinition::getStatuses())->getElementData();
        $status->setSelectOptions($statuses);

        /** @var \SuttonBaker\Impresario\Block\Form\Filter\Select $assignee */
        $filterSet->addFilter(
            $assignee = $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Select::class)
                ->setLabelName('Assignee')
                ->setFormName('assigned_to_id')
        );

        if ($csUsers = $this->getRoleHelper()->getCustomerServiceUsers()) {
            $assignedToUsers = $this->createCollectionSelectConnector()
                ->configure(
                    $csUsers,
                    'ID',
                    'display_name'
                )->getElementData();

            $assignee->setSelectOptions($assignedToUsers);
        }



        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\DateRange::class)
                ->setLabelName('Received')
                ->setFormName('date_received')
        );

        return $this;
    }

    public function indexHandle()
    {
        $openEnquiries = count($this->getEnquiryHelper()->getOpenEnquiries()->load());
        $totalEnquiries = count($this->getEnquiryHelper()->getEnquiryCollection()->load());

        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\FlipCard',
                'enquiries.flip.card'
            )->setShortcode('body_content')
                ->setTemplate('core/flip-card.phtml')
                ->setIcon(\SuttonBaker\Impresario\Definition\Enquiry::ICON)
                ->setHeading('Open Enquiries')
                ->setNumber($openEnquiries)
                ->setColour('slategray')
                ->setProgressPercentage($this->getEnquiryHelper()->getPercentage($openEnquiries, $totalEnquiries))
                ->setProgressHeading("{$openEnquiries} open out of {$totalEnquiries} total enquiries")
                ->setBackLink($this->getUrlHelper()->getPageUrl(PageDefinition::ENQUIRY_LIST))
                ->setBackText('View Enquiries')
                ->setCapabilities($this->getEnquiryHelper()->getViewCapabilities())
        );
    }
}
