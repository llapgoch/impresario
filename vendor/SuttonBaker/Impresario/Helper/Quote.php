<?php

namespace SuttonBaker\Impresario\Helper;

use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
use SuttonBaker\Impresario\Definition\Roles;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Helper
 */
class Quote extends Base
{
    /** @var array  */
    protected $editCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_QUOTE];
    protected $viewCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_QUOTE, Roles::CAP_VIEW_QUOTE];
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
        'po_number',
        'mi_number',
        'nm_mw_number',
        'client_reference',
        'client_requested_by',
        'quote_revision_number'
    ];

/**
 *
 * @param \SuttonBaker\Impresario\Model\Db\Quote $quote
 * @return \SuttonBaker\Impresario\Block\Core\Tile\Tabs
 */
    public function getTabBarForQuote(
        \SuttonBaker\Impresario\Model\Db\Quote $quote
    ) {

        $enquiry = $this->getEnquiryHelper()->getEnquiryForQuote($quote);
        // Get the actual quote which created the project
        $quote = $this->getQuoteHelper()->getQuoteForEnquiry($enquiry->getId());
        $project = $this->getProjectHelper()->getProjectForQuote($quote);

        return $this->getTabBar(
            'quote',
            $enquiry,
            $quote,
            $project
        );
    }


    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $quote
     * @return bool|false|string
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUrlForQuote(
        \SuttonBaker\Impresario\Model\Db\Quote $quote,
        $returnUrl = null
    ) {
        if($quote && $quote->getId()){
            return $this->getUrlHelper()->getPageUrl(
                Page::QUOTE_EDIT,
                ['quote_id' => $quote->getId()],
                $returnUrl
            );
        }

        return false;
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getQuoteCollection($deletedFlag = true, $addOrder = true)
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $collection */
        $collection = $this->createAppObject(
            QuoteDefinition::DEFINITION_COLLECTION
        );

        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);

        if($deletedFlag) {
            $collection->where('{{quote}}.is_deleted=?', '0');
        }

        $collection->joinLeft(
            ['estimator_user' => $userTable],
            "estimator_user.ID={{quote}}.estimator_id",
            ['estimator_name' => 'display_name']
        )->joinLeft(
            ['created_by_user' => $userTable],
            "created_by_user.ID={{quote}}.created_by_id",
            ['created_by_name' => 'display_name']
        )->joinLeft(
            ['completed_by_user' => $userTable],
            "completed_by_user.ID={{quote}}.completed_by_id",
            ['completed_by_name' => 'display_name']
        )->joinLeft(
            "{{client}}",
            "{{client}}.client_id={{quote}}.client_id",
            ['client_name' => 'client_name']
        );

        $collection->columns(
            "CONCAT({{quote}}.status, ':', {{quote}}.tender_status) as aggregate_status"
        );

        if($addOrder){
            $collection->order(new \Zend_Db_Expr(sprintf(
                    "FIELD({{quote}}.tender_status,'%s', '%s', '%s', '%s')",
                    QuoteDefinition::TENDER_STATUS_OPEN,
                    QuoteDefinition::TENDER_STATUS_WON,
                    QuoteDefinition::TENDER_STATUS_CANCELLED,
                    QuoteDefinition::TENDER_STATUS_CLOSED_OUT)
            ))->order(new \Zend_Db_Expr(sprintf(
                "FIELD({{quote}}.status,'%s', '%s', '%s')",
                QuoteDefinition::STATUS_OPEN,
                QuoteDefinition::STATUS_IN_QUERY,
                QuoteDefinition::STATUS_QUOTED)
            ))->order('{{quote}}.date_required');
        }
        return $collection;
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function getOpenQuotes()
    {
        $quotes = $this->getDisplayQuotes()->where(
            'tender_status<>?', QuoteDefinition::TENDER_STATUS_CANCELLED
        )->where(
            '{{quote}}.status<>?', QuoteDefinition::STATUS_QUOTED
        );

        return $quotes;
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
     * @param $status
     * @return string
     */
    public function getTenderStatusDisplayName($status)
    {
        return $this->getDisplayName($status, QuoteDefinition::getTenderStatuses());
    }

    /**
     * @param bool $deletedFlag
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     *
     * Gets a list of the most recent quotes for display
     */
    public function getDisplayQuotes(
        $deletedFlag = true,
        $omitForCompletedProjects = true
    ) {
        $collection = $this->getMasterQuotes($deletedFlag);

        if($omitForCompletedProjects) {
            $collection->joinLeft(
                ['p' => '{{project}}'],
                '{{quote}}.quote_id=p.quote_id 
                    AND p.is_deleted=0',
                []
            )->where('p.status IS NULL OR p.status<>?', ProjectDefinition::STATUS_COMPLETE);
        }
        
        
        return $collection;
    }

    /**
     * Get quotes which are attached to an archived project
     * 
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection
     */
    public function getArchivedQuoteCollection()
    {
        // Don't add the ordering to the quote collection download
        $collection = $this->getMasterQuotes(true, false);
        $collection->order('quote_id ASC');

        $collection->join(
            ['p' => '{{project}}'],
            '{{quote}}.quote_id=p.quote_id 
                AND p.is_deleted=0',
            []
        )->where('p.status=?', ProjectDefinition::STATUS_COMPLETE);
        
        return $collection;
    }

        /**
     * @param bool $deletedFlag
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getMasterQuotes($deletedFlag = true, $addOrder = true)
    {
        return $this->getQuoteCollection($deletedFlag, $addOrder)
            ->where('is_master', 1);
    }

    /**
     * @param $enquiryId
     * @param $status
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getQuoteCollectionForEnquiry($enquiryId, $status)
    {
        $collection = $this->getQuoteCollection();
        $collection->where('enquiry_id=?', $enquiryId);

        if($status) {
            $collection->getSelect()->where('status=?', $status);
        }

        return $collection;
    }

    /**
     * @param $enquiryId
     * @param bool $deletedFlag
     * @return mixed|null|\SuttonBaker\Impresario\Model\Db\Quote
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     * 
     * This will get the quote which made a project, or failing that, the quote with the newest revision number
     */
    public function getQuoteForEnquiry($enquiryId, $deletedFlag = true)
    {
        // Get the quote which has created a project by default,
        // if there's no project then get the newest quote for the enquiry
        if(is_object($enquiryId)){
            $enquiryId = $enquiryId->getId();
        }

        if(!$enquiryId){
            return $this->getQuote();
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $collection */
        $collection = $this->createAppObject(
            QuoteDefinition::DEFINITION_COLLECTION
        );

        $collection->join(
            "{{project}}",
            "{{project}}.quote_id={{quote}}.quote_id",
            []
        );

        $collection->where('{{quote}}.enquiry_id=?', $enquiryId);

        if($deletedFlag){
            $collection->where('{{quote}}.is_deleted=?', 0)
                ->where('{{project}}.is_deleted=?', 0);
        }

        if(count($collection->load())){
            return $collection->firstItem();
        }
        
        return $this->getNewestQuoteForEnquiry($enquiryId, $deletedFlag);
    }

    /**
     * @param $enquiryId
     * @return \SuttonBaker\Impresario\Model\Db\Quote
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function getNewestQuoteForEnquiry($enquiryId, $deletedFlag = true)
    {
        if(is_object($enquiryId)){
            $enquiryId = $enquiryId->getId();
        }

        if($enquiryId) {
            /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $collection */
            $collection = $this->createAppObject(
                QuoteDefinition::DEFINITION_COLLECTION
            );

            $collection
                ->where('enquiry_id=?', $enquiryId)
                ->getSelect()->reset(\Zend_Db_Select::COLUMNS);

            if($deletedFlag){
                $collection->where('is_deleted=?', 0);
            }

            $collection->getSelect()->columns(
                new \Zend_Db_Expr('MAX(revision_number) as revision_number')
            );

            if(!($collection->firstItem()->getRevisionNumber())){
                return $this->getQuote();
            }

            $collection = $this->getQuoteCollection()
                ->where('revision_number=?', $collection->firstItem()->getRevisionNumber())
                ->where('enquiry_id=?', $enquiryId);



            if ($item = $collection->firstItem()) {
                return $item;
            }
        }

        return $this->getQuote();
    }

    /**
     * @param $enquiryId
     * @param null $ignoreQuoteId
     * @return \SuttonBaker\Impresario\Model\Db\Quote\Collection|null
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getQuotesForEnquiry(
        $enquiryId,
        $ignoreQuoteId = null,
        $deletedFlag = true
    ) {
        if(!$enquiryId){
            return null;
        }

        if(is_object($enquiryId)){
            $enquiryId = $enquiryId->getId();
        }

        if(is_object($ignoreQuoteId)){
            $ignoreQuoteId = $ignoreQuoteId->getId();
        }

        $collection = $this->getQuoteCollection($deletedFlag)
            ->where('enquiry_id=?', $enquiryId);

        if($ignoreQuoteId){
            $collection->where('quote_id<>?', $ignoreQuoteId);
        }

        $collection->getSelect()->reset(\Zend_Db_Select::ORDER);
        $collection->getSelect()->order('revision_number DESC');

        return $collection;
    }

    /**
     * @param $number
     * @return string
     *
     * Only supports 1 - 26 at the moment
     */
    public function getRevisionLetter($number)
    {
        $range = range('A', 'Z');

        if(isset($range[$number - 1])){
            return $range[$number - 1];
        }

        return '- -';
    }

    /**
     * @param $project
     * @return \SuttonBaker\Impresario\Model\Db\Quote
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getQuoteForProject($project)
    {
        if(!is_object($project)){
            $project = $this->getProjectHelper()->getProject($project);
        }

        return $this->getQuote($project->getQuoteId());
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

        $quote = $this->getQuoteForEnquiry($enquiryId);

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
            ->setStatus(QuoteDefinition::STATUS_OPEN)
            ->setTenderStatus(QuoteDefinition::TENDER_STATUS_OPEN);

        $quote->save();

        $this->updateMasterFlagsForQuote($quote);
        return $quote;
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
     * @param $data
     * @return bool
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function saveQuoteCreateProjectCheck(
        \SuttonBaker\Impresario\Model\Db\Quote $modelInstance,
        $data
    ) {
        if(isset($data['tender_status']) &&
            $data['tender_status'] == QuoteDefinition::TENDER_STATUS_WON) {

            $project = $this->getProjectHelper()->getProjectForQuote($modelInstance->getId());

            return $project->getId() ? false : true;
        }

        return false;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $quote
     * @return int
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function getRevisionNumber(
        \SuttonBaker\Impresario\Model\Db\Quote $quote
    ) {
        if($quote->getRevisionNumber()){
            return $quote->getRevisionNumber();
        }

        if(!$quote->getEnquiryId()){
            throw new \Exception('Quote does not have an enquiry id');
        }

        $collection = $this->getQuoteCollection(false)
            ->where('enquiry_id=?', $quote->getEnquiryId());

        $collection->getSelect()->columns(
            new \Zend_Db_Expr('MAX(revision_number) as max_revision_number')
        );

        if(!($firstItem = $collection->firstItem())){
            return 1;
        }

        if(!$firstItem->getMaxRevisionNumber()){
            return 1;
        }

        return $firstItem->getMaxRevisionNumber() + 1;
    }


    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $modelInstance
     * @param $data
     * @return array
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function saveQuote(
        \SuttonBaker\Impresario\Model\Db\Quote $modelInstance,
        $data
    ) {
        $returnValues = [
            'quote_id' => null,
            'project_id' => null,
            'project_created' => false,
            'new_save' => false,
            'reopened' => false
        ];

        foreach(QuoteDefinition::NON_USER_VALUES as $nonUserValue){
            if(isset($data[$nonUserValue])){
                unset($data[$nonUserValue]);
            }
        }

        $newSave = false;

        // Add created by user
        if(!$modelInstance->getId()) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
            $newSave = true;
        }

        if($modelInstance->getTenderStatus() !== QuoteDefinition::TENDER_STATUS_OPEN
            && $data['tender_status'] == QuoteDefinition::TENDER_STATUS_OPEN){
                $returnValues['reopened'] = true;
                $data['completed_by_id'] = null;
                $data['date_completed'] = null;
        }

        $returnValues['new_save'] = $newSave;
        $returnValues['quote_id'] = $modelInstance->getId();
        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        $shouldCreateProject = $this->saveQuoteCreateProjectCheck($modelInstance, $data);
        $modelInstance->setData($data)->save();

        if($shouldCreateProject){
            $project = $this->getProjectHelper()->createProjectFromQuote($modelInstance->getId());
            $returnValues['project_created'] = true;
            $returnValues['project_id'] = $project->getId();

            // Save all open Tasks
            $openTasks = $this->getTasksForQuote($modelInstance, TaskDefinition::STATUS_OPEN);
            foreach($openTasks->getItems() as $openTask){
                $openTask->setStatus(TaskDefinition::STATUS_COMPLETE)->save();
            }
        }

        if($newSave && ($temporaryId = $data[\DaveBaker\Core\Definitions\Upload::TEMPORARY_IDENTIFIER_ELEMENT_NAME])){
            // Assign any uploads to the enquiry
            $this->getUploadHelper()->assignTemporaryUploadsToParent(
                $temporaryId,
                \SuttonBaker\Impresario\Definition\Upload::TYPE_QUOTE,
                $modelInstance->getId()
            );
        }

        $this->updateMasterFlagsForQuote($modelInstance);
        return $returnValues;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $quote
     * @param $data
     * @return array
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function reviseQuote(
        \SuttonBaker\Impresario\Model\Db\Quote $quote,
        $data
    ) {
        if(!$quote->getId()){
            throw new \Exception('Revising a quote must be performed on a saved quote');
        }

        $newQuote = $this->duplicateQuote($quote);
        return $this->saveQuote($newQuote, $data);
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $quote
     * @return \SuttonBaker\Impresario\Model\Db\Quote
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function duplicateQuote(
        \SuttonBaker\Impresario\Model\Db\Quote $quote
    ) {
        if(!$quote->getId()){
            return $quote;
        }

        $newQuote = clone $quote;
        $newQuote->unsQuoteId()->unsRevisionNumber()->save();

        if($quote->getPastRevisions()) {
            foreach ($quote->getPastRevisions()->getItems() as $pastRevision) {
                $pastRevision->setParentId($newQuote->getId())->save();
            }
        }

        $quote->setParentId($newQuote->getId())->save();

        return $newQuote;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $quote
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function deleteQuote(
        \SuttonBaker\Impresario\Model\Db\Quote $quote
    ) {
        $tasks = $this->getTaskHelper()->getTaskCollectionForEntity(
            $quote->getId(),
            TaskDefinition::TASK_TYPE_QUOTE
        )->load();

        foreach($tasks as $task){
            $task->setIsDeleted(1)->save();
        }

        $quote->setIsDeleted(1)->save();
        $this->updateMasterFlagsForQuote($quote);
        return $this;
    }

    /**
     *
     * @param int|object $quoteId
     * @return \SuttonBaker\Impresario\Model\Db\Task\Collection
     * 
     * Gets all tasks for all related quote (by enquiry ID)
     */
    public function getTasksForQuote($quote, $status = null)
    {
        if(!is_object($quote)){
            $quote = $this->getQuote($quote);
        }

        $quoteCollection = $this->getQuoteHelper()->getQuotesForEnquiry($quote->getEnquiryId());
        $taskCollection = $this->getTaskHelper()->getTaskCollection()
            ->where('parent_id IN(?)', $quoteCollection->getAllIds())
            ->where('task_type=?', TaskDefinition::TASK_TYPE_QUOTE);

        if($status){
            $taskCollection->where('status=?', $status);
        }

        return $taskCollection;
    }

    /**
     *
     * @param \SuttonBaker\Impresario\Model\Db\Quote $quote
     * @return \SuttonBaker\Impresario\Model\Db\Quote
     */
    public function updateMasterFlagsForQuote(
        \SuttonBaker\Impresario\Model\Db\Quote $quote
    ) {
        // Ascertain whether this quote should be marked as master, mark all others as not 
        $enquiry = $this->getEnquiryHelper()->getEnquiry($quote->getEnquiryId());
        $groupedQuotes = $this->getQuoteHelper()->getQuotesForEnquiry($quote->getEnquiryId(), null, false);
        $masterQuote = $this->getQuoteHelper()->getQuoteForEnquiry($quote->getEnquiryId());
        
        if($masterQuote->getId()){
            $masterQuote->setIsMaster(1)->save();
        }

        foreach($groupedQuotes->getItems() as $gQuote){
            if($gQuote->getId() == $masterQuote->getId()){
                continue;
            }

            $gQuote->setIsMaster(0)->save();
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

    /**
     * @return \SuttonBaker\Impresario\Helper\OutputProcessor\Quote\Status
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getTenderStatusOutputProcessor()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\OutputProcessor\Quote\TenderStatus');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\OutputProcessor\Quote\Revision
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getRevisionOutputProcessor()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\OutputProcessor\Quote\Revision');
    }
}