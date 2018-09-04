<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Base
 * @package SuttonBaker\Impresario\Layout
 */
abstract class Base extends \DaveBaker\Core\Layout\Base
{
    /** @var string  */
    protected $blockPrefix = '';
    protected $headingName = '';
    protected $icon = '';

    /**
     * @return string
     */
    protected function getBlockPrefix()
    {
        return $this->blockPrefix;
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function addMessages()
    {
        $this->addBlock(
            $this->getBlockManager()->getMessagesBlock()->setShortcode('body_content')
        );

        return $this;
    }


    /**
     * @return $this
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function addHeading()
    {
        $this->addBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Heading',
                "{$this->getBlockPrefix()}.form.edit.heading")
                ->setTemplate('core/main-header.phtml')
                ->setShortcode('body_content')
                ->setHeading($this->headingName)
                ->setIcon($this->icon)
        );

        return $this;
    }
}
