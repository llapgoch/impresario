<?php

namespace SuttonBaker\Impresario\Block\Form;
/**
 * Class LargeMessage
 * @package SuttonBaker\Impresario\Block
 */
class LargeMessage extends \DaveBaker\Core\Block\Template
{
    const HEADING_DATA_KEY = 'heading';
    const MESSAGE_DATA_KEY = 'message';
    const MESSAGE_TYPE_DATA_KEY = 'message_type';

    /**
     * @return \DaveBaker\Core\Block\Template
     */
    public function init()
    {
        parent::init();
        $this->setTemplate('form/large-message.phtml');
        $this->setMessageType('warning');
        $this->setHeading('Notice');
    }

    /**
     * @return array|mixed|null
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE_DATA_KEY);
    }

    /**
     * @return $this
     */
    public function setMessage($message)
    {
        $this->setData(self::MESSAGE_DATA_KEY, $message);
        return $this;
    }

    /**
     * @return array|mixed|null
     */
    public function getHeading()
    {
        return $this->getData(self::HEADING_DATA_KEY);
    }

    /**
     * @return $this
     */
    public function setHeading($heading)
    {
        $this->setData(self::HEADING_DATA_KEY, $heading);
        return $this;
    }

    /**
     * @return array|mixed|null
     */
    public function getMessageType()
    {
        return $this->getData(self::MESSAGE_TYPE_DATA_KEY);
    }

    /**
     * @return $this
     */
    public function setMessageType($messageType)
    {
        $this->setData(self::MESSAGE_TYPE_DATA_KEY, $messageType);
        return $this;
    }
}