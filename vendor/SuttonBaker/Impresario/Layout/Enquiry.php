<?php

namespace SuttonBaker\Impresario\Layout;

use SuttonBaker\Impresario\Definition\Page as PageDefinition;

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
        if(!($entityInstance = $this->getApp()->getRegistry()->get('model_instance'))){
            return;
        }

        $this->addHeading();
        $this->addMessages();

        $this->addBlock(
            /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading($this->getEnquiryHelper()->getActionVerb($entityInstance) . " <strong>Enquiry</strong>")
                ->setShortcode('body_content')
                ->addChildBlock($this->getEnquiryHelper()->getTabBarForEnquiry($entityInstance)
            )
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
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading("<strong>Enquiry</strong> List")
                ->setShortcode('body_content')
                ->setTileBodyClass('nopadding table-responsive')
        );

        $mainTile->addChildBlock(
            $createLink = $mainTile->createBlock(
                '\DaveBaker\Core\Block\Html\ButtonAnchor',
                'create.enquiry.link',
                'header_elements'
            )
                ->setTagText('Create a New Enquiry')
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