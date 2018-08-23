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
            ->addAttributes(['fox' => 'socks'])
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Password')
        );

    }
}