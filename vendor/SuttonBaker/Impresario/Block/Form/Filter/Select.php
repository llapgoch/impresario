<?php

namespace SuttonBaker\Impresario\Block\Form\Filter;

class Select 
extends \SuttonBaker\Impresario\Block\Form\Filter
{
    /** @var \DaveBaker\Form\Block\Select */
    protected $mainElement;
    /** @var string */
    protected $template = 'form/filter/filter.phtml';

    /**
     * @return \DaveBaker\Form\Block\Select
     */
    public function getMainElement()
    {
        if(!$this->mainElement){
            $this->mainElement = $this->createBlock(
                \DaveBaker\Form\Block\Select::class,
                null,
                'main_element'
            );
            $this->mainElement->addClass($this->defaultClass);
            $this->addChildBlock($this->mainElement);
        }

        return $this->mainElement;
    }

    /**
     *
     * @param array $options
     * @return $this
     */
    public function setSelectOptions(
        $options
    ) {
        $this->getMainElement()->setSelectOptions($options);
        return $this;
    }

}