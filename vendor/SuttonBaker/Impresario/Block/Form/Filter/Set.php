<?php

namespace SuttonBaker\Impresario\Block\Form\Filter;

class Set 
extends \DaveBaker\Core\Block\Template
{
    /** @var string */
    protected $template = 'form/filter/set.phtml';
    /** @var array */
    protected $filters = [];

    public function addFilter(
        $filter
    ) {
        $this->filters[] = $filter;
        return $this;
    }

    public function getFilters()
    {
        return $this->filters;
    }
}