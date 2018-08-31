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

        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);
        $collection->getSelect()->where('is_deleted=?', '0');

        $collection->joinLeft(
            $userTable,
            "{$userTable}.ID={{quote}}.project_manager_id",
            ['project_manager_name' => 'user_login']
        );

        $collection->joinLeft(
            $userTable,
            "{$userTable}.ID={{quote}}.estimator_id",
            ['estimator_name' => 'user_login']
        );

        $collection->joinLeft(
            $userTable,
            "{$userTable}.ID={{quote}}.created_by_id",
            ['created_by_name' => 'user_login']
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
        $aggregateCollection = $this->getQuoteCollection();

        $aggregateCollection
            ->getSelect()->columns(new \Zend_Db_Expr('MAX(quote_id) as newest_quote_id'))
            ->group('enquiry_id');

        $maxIds = $aggregateCollection->getAllValuesFor('newest_quote_id');
        $collection = $this->getQuoteCollection();

        if(!count($maxIds)){
            return $collection;
        }

        $collection->getSelect()->where('quote_id IN (?)', $maxIds);

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

        $tasksCollection = $this->getTaskHelper()->getTaskCollectionForEntity(
            $quote->getId(),
            TaskDefinition::TASK_TYPE_QUOTE
        );

        $quote->setParentId($quote->getId())->unsQuoteId()->save();

        foreach($tasksCollection->load() as $taskItem){
            $taskItem->setParentId($quote->getId())->save();
        }

        return $quote;
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