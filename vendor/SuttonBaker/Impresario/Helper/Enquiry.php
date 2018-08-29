<?php

namespace SuttonBaker\Impresario\Helper;

use \SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;
/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Helper
 */
class Enquiry extends \DaveBaker\Core\Helper\Base
{

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry\Collection
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Returns a collection of non-deleted enquiries
     */
    public function getEnquiryCollection()
    {
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Enquiry\Collection'
        );

        $collection->getSelect()->where('is_deleted=?', '0');

        return $collection;
    }

    /**
     * @param $enquiryId
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getEnquiry($enquiryId)
    {
        return $this->createAppObject(EnquiryDefinition::DEFINITION_MODEL)->load($enquiryId);
    }
}