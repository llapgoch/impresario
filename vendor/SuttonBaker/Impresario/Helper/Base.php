<?php

namespace SuttonBaker\Impresario\Helper;
/**
 * Class Base
 * @package SuttonBaker\Impresario\Helper
 */
abstract class Base extends \DaveBaker\Core\Helper\Base
{
    /**
     * @param string $key
     * @param array $items
     * @return mixed|string
     */
    protected function getDisplayName($key, $items = []){
        if(isset($items[$key])){
            return $items[$key];
        }

        return '';
    }
    /**
     * @return \SuttonBaker\Impresario\Helper\Task
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getTaskHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Task');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getEnquiryHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Enquiry');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getClientHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Client');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Quote
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getQuoteHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Quote');
    }
}