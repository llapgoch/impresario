<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Layout
 */
class Quote extends \DaveBaker\Core\Layout\Base
{

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function quoteEditHandle()
    {
        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Quote\Form\Edit',
                'quote.form.edit'
            )->setElementName('quote_edit_form')
                ->setShortcode('body_content')
                ->setFormAction($this->getApp()->getHelper('Url')->getCurrentUrl())

        );
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