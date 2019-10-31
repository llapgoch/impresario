<?php

namespace SuttonBaker\Impresario\Block\Form\Filter;

class DateRange
extends \SuttonBaker\Impresario\Block\Form\Filter
{
    /** @var \DaveBaker\Form\Block\Input\Date */
    protected $mainElement;
    /** @var \DaveBaker\Form\Block\Input\Date */
    protected $toElement;
    /** @var string */
    protected $template = 'form/filter/date_range.phtml';
    /** @var string */
    protected $rangeName = '';
    /** @var string */
    protected $defaultLabelName = 'From';
    /** @var string */
    protected $defaultToName = 'To';
    /** @var \DaveBaker\Form\Block\Label */
    protected $toLabel;

    /**
     *
     * @return \DaveBaker\Form\Block\Label
     */
    public function getToLabel()
    {
        if (!$this->toLabel) {
            $this->toLabel = $this->createBlock(
                \DaveBaker\Form\Block\Label::class,
                null,
                'to_label'
            );
            $this->toLabel->setLabelName($this->defaultToName);
            $this->addChildBlock($this->toLabel);
        }

        return $this->toLabel;
    }

    public function getLabel()
    {
        parent::getLabel();
        $this->label->setLabelName($this->defaultLabelName);
        return $this->label;
    }


    public function applyFormNameToElements()
    {
        $name = $this->setFormName . "[" . $this->formName ."][low]";
        $id = $this->setFormName . "_" . $this->formName . "_low" ;

        $this->getMainElement()->setElementName(
            $name
        );

        $this->getMainElement()->addAttribute(['id' => $id]);

        $this->getLabel()->setForId($id);

        $name = $this->setFormName . "[" . $this->formName ."][high]";
        $id = $this->setFormName . "_" . $this->formName . "_high" ;

        $this->getToElement()->setElementName(
            $name
        );

        $this->getToElement()->addAttribute(['id' => $id]);

        $this->getToElement()->setForId($id);

        return $this;
    }

    /**
     * @param [type] $text
     * @return void
     */
    public function setRangeName($text)
    {
        $this->rangeName = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getRangeName()
    {
        return $this->rangeName;
    }

    /**
     * @return \DaveBaker\Form\Block\Select
     */
    public function getMainElement()
    {
        if (!$this->mainElement) {
            $this->mainElement = $this->createBlock(
                \DaveBaker\Form\Block\Input\Date::class,
                null,
                'main_element'
            );

            $this->addChildBlock($this->mainElement);
        }

        return $this->mainElement;
    }

    /**
     * @return \DaveBaker\Form\Block\Select
     */
    public function getToElement()
    {
        if (!$this->toElement) {
            $this->toElement = $this->createBlock(
                \DaveBaker\Form\Block\Input\Date::class,
                null,
                'to_element'
            );

            $this->addChildBlock($this->toElement);
        }

        return $this->toElement;
    }

    protected function _preRender()
    {
        parent::_preRender();
        $this->getToElement();
        $this->getToLabel();
    }
}
