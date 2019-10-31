<?php

namespace SuttonBaker\Impresario\Block\Form\Filter;

class Text 
extends \SuttonBaker\Impresario\Block\Form\Filter
{
    /** @var \DaveBaker\Form\Block\Input\Text */
    protected $mainElement;
    /** @var string */
    protected $template = 'form/filter/filter.phtml';

    /**
     * @return \DaveBaker\Form\Block\Input\Text
     */
    public function getMainElement()
    {
        if(!$this->mainElement){
            $this->mainElement = $this->createBlock(
                \DaveBaker\Form\Block\Input\Text::class,
                null,
                'main_element'
            );

            $this->addChildBlock($this->mainElement);
        }

        return $this->mainElement;
    }

}