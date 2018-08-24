<?php

namespace SuttonBaker\Impresario\Block\Job\Form;

class Create extends \DaveBaker\Form\Block\Form
{
    protected function _preDispatch()
    {
        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Core\Block\Html\Heading')
            ->setHeading('Create a new Job')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text')
                ->setElementName('horse')
            ->addAttributes(['fox' => 'socks'])
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text')
            ->setElementName('goose')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text')
            ->setElementName('moose')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Hidden')
                ->setElementName('action')
                ->setElementValue(1)
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Textarea')
                ->setElementName('bio')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Date')
                ->setElementName('date_field')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Submit')
                ->setElementValue("Submit")
        );

    }
}