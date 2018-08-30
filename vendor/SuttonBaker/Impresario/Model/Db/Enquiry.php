<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Model\Db
 */
class Enquiry extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'enquiry';
        $this->idColumn = 'enquiry_id';

        return $this;
    }

    /**
     * @return Enquiry
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getQuoteEntity()
    {
        $entity = $this->getEnquiryHelper()->getEnquiry();

        if($entityId = $this->getEntityId()){
            $entity->load($entityId);
        }

        return $entity;
    }
}