<?php

namespace SuttonBaker\Impresario\Block\Form\Filter;

class Set 
extends \DaveBaker\Core\Block\Template
{
    /** @var string */
    protected $template = 'form/filter/set.phtml';
    /** @var array */
    protected $filters = [];
    /** @string */
    protected $setFormName = '';

    public function addFilter(
        $filter
    ) {
        $this->filters[] = $filter;
        return $this;
    }

    public function getFilters()
    {
        $this->applySetFormNameToFilters();
        return $this->filters;
    }

    /**
     *
     * @return $this
     */
    public function applySetFormNameToFilters()
    {
        foreach($this->filters as $filter){
            $filter->setSetFormName($this->setFormName);
        }

        return $this;
    }

    /**
     *
     * @param string $setName
     * @return $this
     */
    public function setSetName($setFormName)
    {
        $this->setFormName = $setFormName;
        $this->applySetFormNameToFilters();
        return $this;
    }
}