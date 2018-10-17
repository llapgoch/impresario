<?php

namespace SuttonBaker\Impresario\Api;

use SuttonBaker\Impresario\Session\TableUpdater;

/**
 * Class Base
 * @package SuttonBaker\Impresario\Api
 */
abstract class Base
    extends \DaveBaker\Core\Api\Base
{
    /** @var bool  */
    protected $requiresLogin = true;
    /** @var TableUpdater */
    protected $tableUpdaterSession;

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
     * @return \SuttonBaker\Impresario\Helper\Task
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getTaskHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Task');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Modal
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getModalHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Modal');
    }

    /**
     * @return mixed|TableUpdater
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getTableUpdaterSession()
    {
        if(!$this->tableUpdaterSession){
            $this->tableUpdaterSession = $this->createAppObject(TableUpdater::class);
        }

        return $this->tableUpdaterSession;
    }
}