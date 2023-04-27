<?php

namespace SuttonBaker\Impresario\Helper;

use DaveBaker\Core\Definitions\Upload;
use SuttonBaker\Impresario\Definition\Page;
use SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
use SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use SuttonBaker\Impresario\Definition\Roles;


/**
 * Class Project
 * @package SuttonBaker\Impresario\Helper
 */
class Project extends Base
{
    /** @var array  */
    protected $editCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_PROJECT];
    /** @var array  */
    protected $viewCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_PROJECT, Roles::CAP_VIEW_PROJECT];

    // Note: EDIT_ARCHIVE isn't used at present - we just hide the menu item and listing page for users not with the view 
    /** @var array */
    protected $archiveViewCapabilities = [Roles::CAP_ALL, Roles::CAP_VIEW_ARCHIVE];

    /**
     * @var array
     *
     * Values to bring across when creating a project from a quote
     */
    protected $quoteDataValues = [
        'quote_id',
        'date_received',
        'client_id',
        'site_name',
        'type_id',
        'project_name',
        'client_requested_by',
        'client_reference',
        'date_required',
        'po_number',
        'mi_number',
        'nm_mw_number',
        'net_cost',
        'net_sell',
        'profit',
        'gp'
    ];

    /**
     *
     * @return array
     */
    public function getArchiveViewCapabilities()
    {
        return $this->archiveViewCapabilities;
    }

    /**
     * @param \DaveBaker\Core\Model\Db\BaseInterface $instance
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getActionVerb(\DaveBaker\Core\Model\Db\BaseInterface $instance, $includeView = true)
    {
        if ($includeView && $instance->getStatus() == ProjectDefinition::STATUS_COMPLETE) {
            return 'View';
        }
        return parent::getActionVerb($instance, $includeView);
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Project $project
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getTabBarForProject(
        \SuttonBaker\Impresario\Model\Db\Project $project
    ) {
        $quote = $this->getQuoteHelper()->getQuoteForProject($project);
        $enquiry = $this->getEnquiryHelper()->getEnquiryForQuote($quote);

        return $this->getTabBar(
            'project',
            $enquiry,
            $quote,
            $project
        );
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Project $project
     * @return bool|false|string
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getUrlForProject(
        \SuttonBaker\Impresario\Model\Db\Project $project,
        $returnUrl = null
    ) {
        if ($project && $project->getId()) {
            return $this->getUrlHelper()->getPageUrl(
                Page::PROJECT_EDIT,
                ['project_id' => $project->getId()],
                $returnUrl
            );
        }

        return false;
    }

    /**
     *
     * @return \SuttonBaker\Impresario\Model\Db\Project\Collection
     */
    public function getBaseProjectCollection()
    {
        return $this->createAppObject(
            ProjectDefinition::DEFINITION_COLLECTION
        );
    }
    /**
     * @return \SuttonBaker\Impresario\Model\Db\Project\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getProjectCollection()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Project\Collection $collection */
        $collection = $this->getBaseProjectCollection();

        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);
        $collection->where('{{project}}.is_deleted=?', '0');

        $collection->joinLeft(
            ['project_manager_user' => $userTable],
            "project_manager_user.ID={{project}}.project_manager_id",
            ['project_manager_name' => 'display_name']
        )->joinLeft(
            ['foreman_user' => $userTable],
            "foreman_user.ID={{project}}.assigned_foreman_id",
            ['foreman_name' => 'display_name']
        )->joinLeft(
            ['created_by_user' => $userTable],
            "created_by_user.ID={{project}}.created_by_id",
            ['created_by_name' => 'display_name']
        )->joinLeft(
            "{{client}}",
            "{{client}}.client_id={{project}}.client_id",
            ['client_name' => 'client_name']
        )
        ->joinLeft(
            "{{quote_project_type}}",
            "{{quote_project_type}}.type_id={{project}}.type_id",
            ['type_name' => 'name']
        );

        $collection->order(new \Zend_Db_Expr(
            sprintf(
                "FIELD({{project}}.status,'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')",
                ProjectDefinition::STATUS_OPEN,
                ProjectDefinition::STATUS_PRESTART_BOOKED,
                ProjectDefinition::STATUS_PRESTART_COMPLETED,
                ProjectDefinition::STATUS_RAMS_REQUIRED,
                ProjectDefinition::STATUS_ON_SITE,
                ProjectDefinition::STATUS_ON_SITE_VRF_SUBMITTED,
                ProjectDefinition::STATUS_READY_TO_INVOICE,
                ProjectDefinition::STATUS_READY_TO_SHUTDOWN,
                ProjectDefinition::STATUS_RECALL,
                ProjectDefinition::STATUS_COMPLETE,
                ProjectDefinition::STATUS_CANCELLED
            )
        ))->order('{{project}}.date_required');

        return $collection;
    }

    /**
     * Apply a custom filter to the collection to hide or show cancelled items
     *
     * @param \SuttonBaker\Impresario\Model\Db\Project\Collection $instanceCollection
     * @param int $filter
     * @return void
     */
    public function filterCancelledWhereMap(
        $instanceCollection,
        $filterValue
    ) {
        if (!(int) $filterValue) {
            $instanceCollection->where('status <> ?', ProjectDefinition::STATUS_CANCELLED);
        }
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Project\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getOpenProjects()
    {
        return $this->getProjectCollection()->where('status IN (?)', [
            ProjectDefinition::STATUS_OPEN,
            ProjectDefinition::STATUS_PRESTART_BOOKED,
            ProjectDefinition::STATUS_PRESTART_COMPLETED,
            ProjectDefinition::STATUS_RAMS_REQUIRED
        ]);
    }

    /**
     * @param $status
     * @return string
     */
    public function getStatusDisplayName($status)
    {
        return $this->getDisplayName($status, ProjectDefinition::getStatuses());
    }

    /**
     * @param $quoteId
     * @return mixed|null
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getProjectForQuote($quoteId)
    {
        if (is_object($quoteId)) {
            $quoteId = $quoteId->getId();
        }

        if ($quoteId) {
            $collection = $this->getProjectCollection();
            $collection->getSelect()->where('quote_id=?', $quoteId);

            if ($item = $collection->firstItem()) {
                return $item;
            }
        }

        return $this->getProject();
    }

    /**
     * @param $quoteId
     * @return \DaveBaker\Core\Model\Db\Base|null|\SuttonBaker\Impresario\Model\Db\Project|void
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function createProjectFromQuote($quoteId)
    {
        $quote = $this->getQuoteHelper()->getQuote($quoteId);

        if (!$quote->getId()) {
            return null;
        }

        $project = $this->getProjectForQuote($quoteId);

        if ($project->getId()) {
            throw new \Exception("Project has already been created for quote {$quoteId}");
        }

        $project = $this->getProject();

        foreach ($this->quoteDataValues as $key) {
            $project->setData($key, $quote->getData($key));
        }

        $currentUserId = $this->getUserHelper()->getCurrentUserId();

        $project->setLastEditedById($currentUserId)
            ->setCreatedById($currentUserId)
            ->setStatus(ProjectDefinition::STATUS_OPEN);

        // Get all other quotes for this enquiry and cancel them
        $quotes = $this->getQuoteHelper()->getQuotesForEnquiry($quote->getEnquiryId())
            ->where('quote_id<>?', $quoteId)
            ->load();

        foreach ($quotes as $quote) {
            $quote->setTenderStatus(QuoteDefinition::TENDER_STATUS_CANCELLED)->save();
        }


        return $project->save();
    }

    /**
     * @param $status
     * @return bool
     */
    public function isValidStatus($status)
    {
        return in_array($status, array_keys(ProjectDefinition::getStatuses()));
    }

    /**
     * @param $entityId
     * @return \SuttonBaker\Impresario\Model\Db\Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getProject($entityId = null)
    {
        $entity = $this->createAppObject(ProjectDefinition::DEFINITION_MODEL);

        if ($entityId) {
            $entity->load($entityId);
        }

        return $entity;
    }

    public function saveProject(
        \SuttonBaker\Impresario\Model\Db\Project $modelInstance,
        $data
    ) {

        $defaultChecklistItems = [
            'checklist_plant_off_hired' => 0
        ];

        // This is a cheapo way to deal with checkboxes which by default aren't submitted with the form (because unchecked), but should be 
        // set back to 0 when not checked.
        $data = array_merge($defaultChecklistItems, $data);

        // Set to 1 regardless of form value if positive - stops user being able to change value in HTML
        foreach ($defaultChecklistItems as $checklistKey => $defaultChecklistItem) {
            if ((int) $data[$checklistKey]) {
                $data[$checklistKey] = 1;
            }
        }

        $returnValues = [
            'project_id' => null,
            'project_closed' => false,
            'project_newly_completed' => false,
            'reopened' => false,
            'new_save' => false
        ];

        $isComplete = $modelInstance->isComplete();

        foreach (ProjectDefinition::NON_USER_VALUES as $nonUserValue) {
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

        if(!isset($data['has_rebate']) || !((bool) $data['has_rebate'])) {
            $data['rebate_percentage'] = 0;
        }
        
        $returnValues['new_save'] = $newSave;
        $returnValues['project_id'] = $modelInstance->getId();
        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        $modelInstance->setData($data)->save();

        if ($newSave && ($temporaryItems = $data[Upload::TEMPORARY_IDENTIFIER_ELEMENT_NAME])) {
            foreach ($temporaryItems as $temporaryId => $actualKey) {
                $this->getUploadHelper()->assignTemporaryUploadsToParent(
                    $temporaryId,
                    $actualKey,
                    $modelInstance->getId()
                );
            }
        }

        if (
            $data['status'] == ProjectDefinition::STATUS_COMPLETE
            || $data['status'] == ProjectDefinition::STATUS_CANCELLED
        ) {

            $openTasks = $this->getTaskHelper()->getTaskCollectionForEntity(
                $modelInstance->getId(),
                TaskDefinition::TASK_TYPE_PROJECT,
                TaskDefinition::STATUS_OPEN
            );

            foreach ($openTasks->getItems() as $openTask) {
                $openTask->setStatus(TaskDefinition::STATUS_COMPLETE)->save();
            }
        }

        if (!$isComplete && $modelInstance->isComplete()) {
            $returnValues['project_newly_completed'] = true;
        }

        if ($isComplete && in_array(
            $modelInstance->getStatus(),
            [ProjectDefinition::STATUS_COMPLETE, ProjectDefinition::STATUS_CANCELLED]
        ) == false) {
            $returnValues['reopened'] = true;
        }

        return $returnValues;
    }


    /**
     * @param \SuttonBaker\Impresario\Model\Db\Project $project
     * @return $this
     */
    public function deleteProject(
        \SuttonBaker\Impresario\Model\Db\Project $project
    ) {
        $project->setIsDeleted(1)->save();
        return $this;
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\OutputProcessor\Project\Status
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getStatusOutputProcessor()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\OutputProcessor\Project\Status');
    }
}
