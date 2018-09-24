<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Base
 * @package SuttonBaker\Impresario\Layout
 */
abstract class Base extends \DaveBaker\Core\Layout\Base
{
    /** @var string  */
    protected $blockPrefix = '';
    protected $headingName = '';
    protected $icon = '';
    protected $headingShortcode = 'body_content';

    /**
     * @return string
     */
    protected function getBlockPrefix()
    {
        return $this->blockPrefix;
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function addMessages()
    {
        $this->addBlock(
            $this->getBlockManager()->getMessagesBlock()->setShortcode('body_content')
        );

        return $this;
    }


    /**
     * @return $this
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function addHeading()
    {
        $this->addBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Heading',
                "{$this->getBlockPrefix()}.form.edit.heading")
                ->setTemplate('core/main-header.phtml')
                ->setShortcode($this->headingShortcode)
                ->setHeading($this->headingName)
                ->setIcon($this->icon)
        );

        return $this;
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Task
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getTaskHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Task');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getEnquiryHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Enquiry');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getClientHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Client');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Quote
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getQuoteHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Quote');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getProjectHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Project');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Invoice
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getInvoiceHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Invoice');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Variation
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getVariationHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Variation');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Modal
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getModalHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Modal');
    }
}
