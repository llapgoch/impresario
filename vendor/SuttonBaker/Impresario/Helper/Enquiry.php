<?php

namespace SuttonBaker\Impresario\Helper;

use DaveBaker\Core\Definitions\Messages;
use DaveBaker\Core\Definitions\Upload;
use DaveBaker\Core\Helper\Exception;
use \SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;
use SuttonBaker\Impresario\Definition\Flow;
use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use \SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Helper
 */
class Enquiry
    extends Base
{
    /** @var array  */
    protected $editCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_ENQUIRY];
    protected $viewCapabilities = [Roles::CAP_ALL, Roles::CAP_VIEW_ENQUIRY, Roles::CAP_EDIT_ENQUIRY];


    /**
     * @param \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function getTabBarForEnquiry(
        \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry
    ) {
        $quote = $this->getQuoteHelper()->getNewestQuoteForEnquiry($enquiry, !((bool)$enquiry->getIsDeleted()));
        $project = $this->getProjectHelper()->getProjectForQuote($quote);

        return $this->getTabBar(
            'enquiry',
            $enquiry,
            $quote,
            $project
        );
    }
    /**
     * @param \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry
     * @return bool|false|string
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUrlForEnquiry(
        \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry,
        $returnUrl = null
    ) {
        if($enquiry && $enquiry->getId()){
            return $this->getUrlHelper()->getPageUrl(
                Page::ENQUIRY_EDIT,
                ['enquiry_id' => $enquiry->getId()],
                $returnUrl
            );
        }

        return false;
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getEnquiryCollection($deletedFlag = true)
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Enquiry\Collection $collection */
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Enquiry\Collection'
        );

        if($deletedFlag) {
            $collection->getSelect()->where('is_deleted=?', '0');
        }

        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);

        $collection->joinLeft(
            ['user_assigned' => $userTable],
            "user_assigned.ID={{enquiry}}.assigned_to_id",
            ['assigned_to_name' => 'user_login']
        );

        $collection->joinLeft(
            ['engineer_user' => $userTable],
            "engineer_user.ID={{enquiry}}.engineer_id",
            ['engineer_name' => 'user_login']
        );

        $collection->order(new \Zend_Db_Expr(sprintf(
                "FIELD({{enquiry}}.status,'%s', '%s', '%s', '%s', '%s', '%s')",
                EnquiryDefinition::STATUS_OPEN,
                EnquiryDefinition::STATUS_ENGINEER_ASSIGNED,
                EnquiryDefinition::STATUS_REPORT_COMPLETE,
                EnquiryDefinition::STATUS_INVOICED,
                EnquiryDefinition::STATUS_COMPLETE,
                EnquiryDefinition::STATUS_CANCELLED)
        ))->order('{{enquiry}}.target_date');

        return $collection;
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getOpenEnquiries()
    {
        return $this->getEnquiryCollection()->where('status<>?', EnquiryDefinition::STATUS_COMPLETE);
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
     * @param int|null $enquiryId
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getEnquiry($enquiryId = null)
    {
        $enquiry = $this->createAppObject(EnquiryDefinition::DEFINITION_MODEL);

        if($enquiryId){
            $enquiry->load($enquiryId);
        }

        return $enquiry;
    }

    /**
     * @param $quote
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getEnquiryForQuote($quote)
    {
        if(!is_object($quote)){
            $quote = $this->getQuoteHelper()->getQuote($quote);
        }

       return $this->getEnquiry($quote->getEnquiryId());
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function deleteEnquiry(
        \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry
    ) {
        if(!$enquiry->getId()){
            return;
        }

        $tasks = $this->getTaskHelper()->getTaskCollectionForEntity(
            $enquiry->getId(),
            TaskDefinition::TASK_TYPE_ENQUIRY
        )->load();

        foreach($tasks as $task){
            $task->setIsDeleted(1)->save();
        }

        $enquiry->setIsDeleted(1)->save();
    }

    /**
     * @param array $data
     * @return $this|array
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function saveEnquiry(
        \SuttonBaker\Impresario\Model\Db\Enquiry $modelInstance,
        $data
    ) {
        $returnValues = [
            'enquiry_id' => null,
            'quote_id' => null,
            'quote_created' => false
        ];

        foreach(EnquiryDefinition::NON_USER_VALUES as $nonUserValue){
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

        $returnValues['new_save'] = $newSave;
        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        $modelInstance->setData($data)->save();
        $returnValues['enquiry_id'] = $modelInstance->getId();

        if($newSave && ($temporaryId = $data[Upload::TEMPORARY_IDENTIFIER_ELEMENT_NAME])){
            // Assign any uploads to the enquiry
            $this->getUploadHelper()->assignTemporaryUploadsToParent(
                $temporaryId,
                \SuttonBaker\Impresario\Definition\Upload::TYPE_ENQUIRY,
                $modelInstance->getId()
            );
        }

        // Create a quote if enquiry is complete
        if($data['status'] == EnquiryDefinition::STATUS_COMPLETE){
            $quote = $this->getQuoteHelper()->getNewestQuoteForEnquiry($modelInstance->getId());

            if(!$quote->getId()) {
                $quote = $this->getQuoteHelper()->createQuoteFromEnquiry($modelInstance->getId());
                $returnValues['quote_created'] = true;
                $returnValues['quote_id'] = $quote->getId();
                return $returnValues;
            }
        }

        return $returnValues;
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\OutputProcessor\Enquiry\Status
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getStatusOutputProcessor()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\OutputProcessor\Enquiry\Status');
    }
}