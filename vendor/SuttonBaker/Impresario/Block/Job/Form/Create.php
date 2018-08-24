<?php

namespace SuttonBaker\Impresario\Block\Job\Form;

class Create extends \DaveBaker\Form\Block\Form
{
    protected function _preDispatch()
    {
        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Core\Block\Html\Heading', 'title')
            ->setHeading('Create a new Job')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'horse')
                ->setElementName('horse')
            ->addAttributes(['fox' => 'socks'])
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
            $this->createBlock('\DaveBaker\Form\Block\Input\Date', 'date_field')
                ->setElementName('date_field')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Submit', 'submit')
                ->setElementValue("Submit")
        );

    }
}