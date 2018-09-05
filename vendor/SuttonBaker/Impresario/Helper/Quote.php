<?php

namespace SuttonBaker\Impresario\Helper;

use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Helper
 */
class Quote extends Base
{
    /**
     * @var array
     *
     * Values to bring across when creating a quote from an enquiry
     */
    protected $enquiryDataValues = [
        'date_received',
        'client_id',
        'enquiry_id',
        'site_name',
        'client_reference'
    ];

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Returns a collection of non-deleted tasks
     */
    public function getQuoteCollection()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $collection */
        $collection = $this->createAppObject(
            QuoteDefinition::DEFINITION_COLLECTION
        );

        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);
        $collection->where('{{quote}}.is_deleted=?', '0');

        $collection->joinLeft(
            ['project_manager_user' => $userTable],
            "project_manager_user.ID={{quote}}.project_manager_id",
            ['project_manager_name' => 'user_login']
        );

        $collection->joinLeft(
            ['estimator_user' => $userTable],
            "estimator_user.ID={{quote}}.estimator_id",
            ['estimator_name' => 'user_login']
        );

        $collection->joinLeft(
            ['created_by_user' => $userTable],
            "created_by_user.ID={{quote}}.created_by_id",
            ['created_by_name' => 'user_login']
        );

        $collection->joinLeft(
            "{{client}}",
            "{{client}}.client_id={{quote}}.client_id",
            ['client_name' => 'client_name']
        );

        return $collection;
    }

    /**
     * @param $status
     * @return string
     */
    public function getStatusDisplayName($status)
    {
        return $this->getDisplayName($status, QuoteDefinition::getStatuses());
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     *
     * Gets a list of the most recent quotes for display
     */
    public function getDisplayQuotes()
    {
        $collection = $this->getQuoteCollection()
            ->where('is_superseded=0');


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
     * @param $enquiryId
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function getNewestQuoteForEnquiry($enquiryId)
    {
        $collection = $this->getDisplayQuotes()
            ->where('enquiry_id=?', $enquiryId);

        $items = $collection->load();

        if(count($items)){
            return $items[0];
        }

        return $this->getQuote();
    }

    /**
     * @param $enquiryId
     * @return \DaveBaker\Core\Model\Db\Base|null|\SuttonBaker\Impresario\Model\Db\Quote|null
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     * @throws \Exception
     */
    public function createQuoteFromEnquiry($enquiryId)
    {
        $enquiry = $this->getEnquiryHelper()->getEnquiry($enquiryId);

        if(!$enquiry->getId()){
            return null;
        }

        $quote = $this->getNewestQuoteForEnquiry($enquiryId);

        if($quote->getId()){
            throw new \Exception("Quote has already been created for enquiry {$enquiryId}");
        }

        foreach($this->enquiryDataValues as $key){
            $quote->setData($key, $enquiry->getData($key));
        }

        $currentUserId = $this->getUserHelper()->getCurrentUserId();

        $quote->setLastEditedById($currentUserId)
            ->setCreatedById($currentUserId)
            ->setCreatedBy($currentUserId)
            ->setStatus(QuoteDefinition::STATUS_OPEN);

        return $quote->save();
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
     * @return \SuttonBaker\Impresario\Model\Db\Quote
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

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $quote
     * @return \SuttonBaker\Impresario\Model\Db\Quote
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function duplicateQuote(
        \SuttonBaker\Impresario\Model\Db\Quote $quote
    ) {
        if(!$quote->getId()){
            return $quote;
        }

        $newQuote = clone $quote;

        $newQuote->setParentId($quote->getId())
            ->unsQuoteId()
            ->save();

        $quote->setIsSuperseded(1)->save();

        return $quote;
    }

    /**
     * @param $parentId
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getSupersededQuotesForParent($parentId)
    {
        $collection = $this->getQuoteCollection()
            ->where('parent_id=?', $parentId)
            ->where('is_superseded', 1);

        return $collection;
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\OutputProcessor\Quote\Status
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getStatusOutputProcessor()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\OutputProcessor\Quote\Status');
    }

}