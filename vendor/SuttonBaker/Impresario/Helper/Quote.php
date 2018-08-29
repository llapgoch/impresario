<?php

namespace SuttonBaker\Impresario\Helper;

use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Helper
 */
class Quote extends \DaveBaker\Core\Helper\Base
{

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Returns a collection of non-deleted tasks
     */
    public function getQuoteCollection()
    {
        $collection = $this->createAppObject(
            QuoteDefinition::DEFINITION_COLLECTION
        );

        $collection->getSelect()->where('is_deleted=?', '0');

        return $collection;
    }

    /**
     * @param string $entity
     * @param string $status
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getQuoteCollectionForEnquiry($enquiryId, $status)
    {
        $collection = $this->getQuoteCollection();

        $collection->getSelect()->where('enquiry_id=?', $enquiryId);

        if($status) {
            $collection->getSelect()->where('status=?', $status);
        }

        return $collection;
    }

    /**
     * @param $status
     * @return bool
     */
    public function isValidStatus($status)
    {
        return in_array($status, array_keys(\SuttonBaker\Impresario\Definition\Quote::getStatuses()));
    }

    /**
     * @param $entityId
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getQuote($entityId = null)
    {
        $entity = $this->createAppObject(QuoteDefinition::DEFINITION_MODEL);

        if($entityId){
            $entity->load($entityId);
        }

        return $entity;
    }

}