<?php

namespace SuttonBaker\Impresario\Block\Form\Filter;

class Set 
extends \DaveBaker\Core\Block\Template
{
    /** @var string */
    protected $template = 'form/filter/set.phtml';
    /** @var array */
    protected $filters = [];
    /** @var string */
    protected $setFormName = '';
    /** @var bool */
    protected $showResetButton = true;
    /** @var \DaveBaker\Form\Block\Button */
    protected $resetButton;
    /** @var string */
    protected $resetLabel = 'Reset Filter';

    protected function _construct()
    {
        parent::_construct();
        $this->addTagIdentifier('filter-set');
    }

    protected function _preDispatch()
    {
        wp_register_script('impresario_filter_set', get_template_directory_uri() . '/assets/js/filter.set.widget.js', ['jquery', 'impresario_serialize_object']);
        wp_enqueue_script('impresario_filter_set');
    }

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
     * @return \DaveBaker\Form\Block\Button
     */
    public function getResetButton()
    {
        if(!$this->resetButton){
            $this->resetButton = $this->createBlock(
                \DaveBaker\Form\Block\Button::class,
                "{$this->setFormName}.reset_button",
                'reset_button'
            )->setButtonName($this->resetLabel)
            ->addAttribute(['type' => 'reset']);
        }

        return $this->resetButton;
    }

    /**
     *
     * @return bool
     */
    public function getShowResetButton()
    {
        return $this->showResetButton;
    }

    /**
     *
     * @param bool $show
     * @return $this
     */
    public function setShowResetButton($show)
    {
        $this->showResetButton = (bool) $show;
        return $this;
    }

    /**
     *
     * @param string $formElementName
     * @param mixed $value
     * @return $this
     */
    public function setFilterValue(
        $formElementName, 
        $value
    ) {
        
        /** @var \SuttonBaker\Impresario\Block\Form\Filter $filter */
        foreach($this->getFilters() as $filter) {
            if($filter->getFormName() == $formElementName){
                $filter->setFormValue($value);
            }
        }

        return $this;
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