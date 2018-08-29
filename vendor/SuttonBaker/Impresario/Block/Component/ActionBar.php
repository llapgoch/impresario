<?php

namespace SuttonBaker\Impresario\Block\Component;

class ActionBar extends \DaveBaker\Core\Block\Template
{
    /** @var array  */
    protected $actions = [];

    protected function init()
    {
        $this->setTemplate('impresario/components/action_bar.phtml');
    }

    /**
     * @param $linkText
     * @param $link
     * @param array $attributes
     * @param array $classes
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function addActionItem(
        $linkText, $link, $attributes = [], $classes = []
    ) {
        /** @var \DaveBaker\Core\Helper\Util $helper */
        $helper = $this->getApp()->getHelper('Util');
        $this->actions[$helper->createUrlKeyFromText($linkText)] = [
            'linkText' => $linkText,
            'link' => $link,
            'attributes' => $attributes,
            'classes' => $classes
        ];


        return $this;
    }

    /**
     * @return array
     */
    public function getActionsItems()
    {
        return $this->actions;
    }

}