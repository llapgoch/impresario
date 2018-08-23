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

        

    }
}