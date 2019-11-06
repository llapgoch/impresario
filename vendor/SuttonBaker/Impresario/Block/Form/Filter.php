<?php

namespace SuttonBaker\Impresario\Block\Form;

abstract class Filter
extends \DaveBaker\Core\Block\Template
{
    /** @var \DaveBaker\Form\Block\Label */
    protected $label;
    /** @var string */
    protected $setFormName = '';
    /** @var string */
    protected $formName = '';
    /** @var string */
    protected $defaultClass = 'js-filter-item';

    /** @return \DaveBaker\Form\Block\ValueSetterInterface */
    public abstract function getMainElement();

    protected function _construct()
    {
        parent::_construct();
        $this->addTagIdentifier('filter-item');
    }
    /**
     *
     * @return \DaveBaker\Form\Block\Label
     */
    public function getLabel()
    {
        if (!$this->label) {
            $this->label = $this->createBlock(
                \DaveBaker\Form\Block\Label::class,
                null,
                'label'
            );

            $this->addChildBlock($this->label);
        }

        return $this->label;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setFormValue($value)
    {
        $this->getMainElement()->setElementValue($value);
        return $this;
    }

    /**
     *
     * @param string $text
     * @return $this
     */
    public function setLabelName($text)
    {
        $this->getLabel()->setLabelName($text);
        return $this;
    }

    /**
     *
     * @param string $text
     * @return $this
     */
    public function setFormName($name)
    {
        $this->formName = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormName()
    {
        return $this->formName;
    }

    /**
     *
     * @return $this
     */
    public function applyFormNameToElements()
    {
        $name = $this->formName;
        $id = $this->setFormName . "_" . $this->formName;

        $this->getMainElement()->setElementName(
            $name
        );

        $this->getMainElement()->addAttribute(['id' => $id]);

        $this->getLabel()->setForId($id);

        return $this;
    }

    /**
     *
     * @param string $setFormName
     * @return $this
     */
    public function setSetFormName($setFormName)
    {
        $this->setFormName = $setFormName;
        $this->applyFormNameToElements();
        return $this;
    }

    protected function _preRender()
    {
        if (!$this->formName || !$this->setFormName) {
            throw new \Exception('Set name or set form name not set');
        }
        $this->getLabel();
        $this->getMainElement();
        $this->applyFormNameToElements();
    }
}
