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
use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;

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
        $quote = $this->getQuoteHelper()->getQuoteForEnquiry($enquiry, !((bool)$enquiry->getIsDeleted()));
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
        if ($enquiry && $enquiry->getId()) {
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

        if ($deletedFlag) {
            $collection->where('{{enquiry}}.is_deleted=?', '0');
        }

        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);

        $collection->joinLeft(
            ['user_assigned' => $userTable],
            "user_assigned.ID={{enquiry}}.assigned_to_id",
            ['assigned_to_name' => 'display_name']
        );

        $collection->joinLeft(
            "{{client}}",
            "{{client}}.client_id={{enquiry}}.client_id",
            ['client_name' => 'client_name']
        );

        $collection->joinLeft(
            ['engineer_user' => $userTable],
            "engineer_user.ID={{enquiry}}.engineer_id",
            ['engineer_name' => 'display_name']
        );

        $collection->order(new \Zend_Db_Expr(
            sprintf(
                "FIELD({{enquiry}}.status,'%s', '%s', '%s', '%s', '%s', '%s')",
                EnquiryDefinition::STATUS_OPEN,
                EnquiryDefinition::STATUS_ENGINEER_ASSIGNED,
                EnquiryDefinition::STATUS_READY_TO_INVOICE,
                EnquiryDefinition::STATUS_INVOICED,
                EnquiryDefinition::STATUS_COMPLETE,
                EnquiryDefinition::STATUS_CANCELLED
            )
        ))->order('{{enquiry}}.target_date');

        return $collection;
    }

    /**
     * @param boolean $deletedFlag
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry\Collection
     */
    public function getDisplayEnquiries($deletedFlag = true)
    {
        $collection = $this->getEnquiryCollection($deletedFlag)
            ->joinLeft(
                ['q' => '{{quote}}'],
                '{{enquiry}}.enquiry_id=q.enquiry_id 
                AND q.is_deleted=0 
                AND q.is_master=1',
                []
            )->joinLeft(
                ['p' => '{{project}}'],
                'q.quote_id=p.quote_id 
                AND p.is_deleted=0',
                []
            )->where('p.status IS NULL or p.status<>?', ProjectDefinition::STATUS_COMPLETE);


        return $collection;
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getOpenEnquiries()
    {
        return $collection = $this->getEnquiryCollection()->where('status IN (?)', [
            EnquiryDefinition::STATUS_OPEN,
            EnquiryDefinition::STATUS_ENGINEER_ASSIGNED,
            EnquiryDefinition::STATUS_READY_TO_INVOICE
        ]);
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

        if ($enquiryId) {
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
        if (!is_object($quote)) {
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
        if (!$enquiry->getId()) {
            return;
        }

        $tasks = $this->getTaskHelper()->getTaskCollectionForEntity(
            $enquiry->getId(),
            TaskDefinition::TASK_TYPE_ENQUIRY
        )->load();

        foreach ($tasks as $task) {
            $task->setIsDeleted(1)->save();
        }

        $enquiry->setIsDeleted(1)->save();
    }

    public function isEnquiryLocked(
        \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry
    ) {
        if (!$enquiry->getId()) {
            return false;
        }

        return in_array(
            $enquiry->getStatus(),
            [EnquiryDefinition::STATUS_COMPLETE, EnquiryDefinition::STATUS_CANCELLED]
        );
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
            'quote_created' => false,
            'reopened' => false
        ];

        foreach (EnquiryDefinition::NON_USER_VALUES as $nonUserValue) {
            if (isset($data[$nonUserValue])) {
                unset($data[$nonUserValue]);
            }
        }

        $newSave = false;

        // Add created by user
        if (!$modelInstance->getId()) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
            $newSave = true;
        }

        $returnValues['new_save'] = $newSave;
        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        if ($modelInstance->isComplete() && $data['status'] !== EnquiryDefinition::STATUS_COMPLETE) {
            $data['date_completed'] = null;
            $returnValues['reopened'] = true;
        }

        $modelInstance->setData($data)->save();
        $returnValues['enquiry_id'] = $modelInstance->getId();

        if ($newSave && ($temporaryItems = $data[Upload::TEMPORARY_IDENTIFIER_ELEMENT_NAME])) {

            foreach ($temporaryItems as $temporaryId => $actualKey) {
                $this->getUploadHelper()->assignTemporaryUploadsToParent(
                    $temporaryId,
                    $actualKey,
                    $modelInstance->getId()
                );
            }
        }

        // Create a quote if enquiry is complete
        if ($data['status'] == EnquiryDefinition::STATUS_COMPLETE) {
            $quote = $this->getQuoteHelper()->getQuoteForEnquiry($modelInstance->getId());

            if (!$quote->getId()) {
                $quote = $this->getQuoteHelper()->createQuoteFromEnquiry($modelInstance->getId());
                $returnValues['quote_created'] = true;
                $returnValues['quote_id'] = $quote->getId();
                return $returnValues;
            }
        }

        // Close open tasks if enquiry is cancelled or complete
        if (
            $data['status'] == EnquiryDefinition::STATUS_COMPLETE
            || $data['status'] == EnquiryDefinition::STATUS_CANCELLED
        ) {
            // Save all open Tasks
            $openTasks = $this->getTaskHelper()->getTaskCollectionForEntity(
                $modelInstance->getId(),
                TaskDefinition::TASK_TYPE_ENQUIRY,
                TaskDefinition::STATUS_OPEN
            );

            foreach ($openTasks->getItems() as $openTask) {
                $openTask->setStatus(TaskDefinition::STATUS_COMPLETE)->save();
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
