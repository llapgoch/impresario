<?php

namespace SuttonBaker\Impresario\Api;

class Base
    extends \DaveBaker\Core\Api\Base
{
    /** @var bool  */
    protected $requiresLogin = true;

    /**
     * @return \SuttonBaker\Impresario\Helper\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getEnquiryHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Enquiry');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Quote
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getQuoteHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Quote');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Modal
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getModalHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Modal');
    }
}