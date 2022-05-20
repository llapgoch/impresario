<?php

namespace SuttonBaker\Impresario\Api;

use SuttonBaker\Impresario\Session\TableUpdater;
use DaveBaker\Core\Installer\Exception;

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
     *
     * @param array $params
     * @param \DaveBaker\Core\Model\Db\BaseInterface $object
     * @return array
     * @throws Exception
     */
    protected function performRecordMonitor(
        $params,
        $object
    ) {
        if (!isset($params['timestamp'])) {
            throw new Exception('Timestamp not set');
        }

        if (!$object->getId() || $object->getIsDeleted()) {
            throw new Exception("Record not found");
        }

        $updated = false;

        if ((int) $object->getLastEditedById() !== (int) $this->getUserHelper()->getCurrentUserId()) {
            if ((int) $params['timestamp'] < strtotime($object->getUpdatedAt())) {
                $updated = true;
            }
        }

        return [
            'updated' => $updated
        ];
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
     * @return \SuttonBaker\Impresario\Helper\Variation
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getVariationHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Variation');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Client
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getClientHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Client');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Supplier
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getSupplierHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Supplier');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Invoice
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getInvoiceHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Invoice');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Cost
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getCostHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Cost');
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
     * @return \SuttonBaker\Impresario\Helper\Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getProjectHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Project');
    }

    /**
     * @return mixed|TableUpdater
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getTableUpdaterSession()
    {
        if (!$this->tableUpdaterSession) {
            $this->tableUpdaterSession = $this->createAppObject(TableUpdater::class);
        }

        return $this->tableUpdaterSession;
    }
}
