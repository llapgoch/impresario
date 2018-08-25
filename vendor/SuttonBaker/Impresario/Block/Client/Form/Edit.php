<?php

namespace SuttonBaker\Impresario\Block\Client\Form;
/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \DaveBaker\Form\Block\Form
{
    /**
     * @return \DaveBaker\Form\Block\Form|void
     * @throws \DaveBaker\Core\App\Exception
     */
    protected function _preDispatch()
    {
        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Core\Block\Html\Heading', 'title')
            ->setHeading('Edit Client')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'name')
                ->setElementName('horse')
            ->addAttribute(['fox' => 'socks'])
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'goose')
            ->setElementName('goose')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'moose')
            ->setElementName('moose')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Hidden', 'action')
                ->setElementName('action')
                ->setElementValue(1)
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Textarea', 'bio')
                ->setElementName('bio')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'date_field')
                ->setElementName('date_field')
                ->addClass('js-date-picker')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Submit', 'submit')
                ->setElementValue("Submit")
        );

    }
}