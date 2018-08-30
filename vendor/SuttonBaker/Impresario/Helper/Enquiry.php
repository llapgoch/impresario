<?php

namespace SuttonBaker\Impresario\Helper;

use \SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;
/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Helper
 */
class Enquiry extends Base
{

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry\Collection
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Returns a collection of non-deleted enquiries
     */
    public function getEnquiryCollection()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Enquiry\Collection $collection */
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Enquiry\Collection'
        );

        $collection->getSelect()->where('is_deleted=?', '0');
        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);

        $collection->joinLeft(
            $userTable,
            "{$userTable}.ID={{enquiry}}.project_manager_id",
            ['project_manager_name' => 'user_login']
        );

        $collection->joinLeft(
            $userTable,
            "{$userTable}.ID={{enquiry}}.engineer_id",
            ['engineer_name' => 'user_login']
        );

        return $collection;
    }

    /**
     * @param $status
     * @return string
     */
    public function getStatusDisplayName($status)
    {
        return $this->getDisplayName($status, EnquiryDefinition::getStatuses());
    }

    /**
     * @param $enquiryId
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getEnquiry($enquiryId = '')
    {
        $enquiry = $this->createAppObject(EnquiryDefinition::DEFINITION_MODEL);

        if($enquiryId){
            $enquiry->load($enquiryId);
        }

        return $enquiry;
    }
}