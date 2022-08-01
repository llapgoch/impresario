<?php

namespace Suttonbaker\Impresario\Block\Form\Input;

/**
 * Class FixedCheckbox
 * @package DaveBaker\Form\Block\Input
 */
class FixedCheckbox
extends \DaveBaker\Form\Block\Input\Input
implements \DaveBaker\Form\Block\ValueSetterInterface
{
    protected function _preRender()
    {
        if((int) $this->getElementValue() === (int) $this->getFixedValue()) {
            $this->addAttribute(['checked' => 'checked']);
        }
    }

    /**
     * @return \DaveBaker\Form\Block\Base|void
     */
    protected function init()
    {
        parent::init();
        $this->setTemplate('form/input/fixed-value-checkbox.phtml');
    }
    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _construct()
    {
        $this->setElementType('checkbox');
        parent::_construct();
        // Checkboxes shouldn't have the form-control class
        $this->removeTagIdentifier('input');
    }
}
