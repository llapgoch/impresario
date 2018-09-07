<?php

namespace SuttonBaker\Impresario\Layout;
use SuttonBaker\Impresario\Definition\Page as PageDefinition;

/**
 * Class Client
 * @package SuttonBaker\Impresario\Layout
 */
class GlobalLayout extends Base
{
    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function defaultHandle()
    {
        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Structure\Nav',
                'main.sidebar.nav'
            )->setShortcode('impressario_nav_items')
            ->setTemplate('nav/sidebar.phtml')
        );
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function indexHandle()
    {
//        $this->getBlockManager()->removeBlock('main.sidebar.nav');

        $openEnquiries = count($this->getEnquiryHelper()->getOpenEnquiries()->load());
        $totalEnquiries = count($this->getEnquiryHelper()->getEnquiryCollection()->load());

        $openQuotes = count($this->getQuoteHelper()->getOpenQuotes()->load());
        $totalQuotes = count($this->getQuoteHelper()->getDisplayQuotes()->load());

        $openProjects = count($this->getProjectHelper()->getOpenProjects()->load());
        $totalProjects = count($this->getProjectHelper()->getProjectCollection()->load());

        $openTasks = count($this->getTaskHelper()->getOpenTasks()->load());
        $totalTasks = count($this->getTaskHelper()->getTaskCollection()->load());

//        $this->getEnquiryHelper()->getEnquiryCollection()->where()

        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\FlipCard',
                'enquiries.flip.card'
            )->setShortcode('body_content')
                ->setTemplate('core/flip-card.phtml')
                ->setIcon('fa-thumb-tack')
                ->setHeading('Open Enquiries')
                ->setNumber($openEnquiries)
                ->setColour('slategray')
                ->setBackLink($this->getUrlHelper()->getPageUrl(PageDefinition::CLIENT_LIST))
                ->setBackText('View Enquiries')
        );

        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\FlipCard',
                'quotes.flip.card'
            )->setShortcode('body_content')
                ->setTemplate('core/flip-card.phtml')
                ->setIcon('fa-calculator')
                ->setHeading('Open Quotes')
                ->setNumber($openQuotes)
                ->setColour('greensea')
                ->setBackLink($this->getUrlHelper()->getPageUrl(PageDefinition::CLIENT_LIST))
                ->setBackText('View Quotes')
        );

        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\FlipCard',
                'projects.flip.card'
            )->setShortcode('body_content')
                ->setTemplate('core/flip-card.phtml')
                ->setIcon('fa-ravelry')
                ->setHeading('Open Projects')
                ->setNumber($openProjects)
                ->setColour('amethyst')
                ->setBackLink($this->getUrlHelper()->getPageUrl(PageDefinition::CLIENT_LIST))
                ->setBackText('View Projects')
        );

        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\FlipCard',
                'tasks.flip.card'
            )->setShortcode('body_content')
                ->setTemplate('core/flip-card.phtml')
                ->setIcon('fa fa-th-list')
                ->setHeading('Open Tasks')
                ->setNumber($openTasks)
                ->setColour('cyan')
                ->setBackLink($this->getUrlHelper()->getPageUrl(PageDefinition::CLIENT_LIST))
                ->setBackText('View Tasks')
        );
    }
}