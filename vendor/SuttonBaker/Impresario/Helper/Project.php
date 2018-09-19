<?php

namespace SuttonBaker\Impresario\Helper;

use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Project
 * @package SuttonBaker\Impresario\Helper
 */
class Project extends Base
{
    /** @var array  */
    protected $editCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_PROJECT];
    protected $viewCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_PROJECT, Roles::CAP_VIEW_PROJECT];
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
        'project_name',
        'client_requested_by',
        'client_reference',
        'date_required',
        'project_manager_id',
        'po_number',
        'mi_number',
        'nm_mw_number',
        'net_cost',
        'net_sell',
        'comments'
    ];

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Project $project
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
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
        if($project && $project->getId()){
            return $this->getUrlHelper()->getPageUrl(
                Page::PROJECT_EDIT,
                ['project_id' => $project->getId()],
                $returnUrl
            );
        }

        return false;
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Project\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getProjectCollection()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Project\Collection $collection */
        $collection = $this->createAppObject(
            ProjectDefinition::DEFINITION_COLLECTION
        );

        $userTable = $this->getApp()->getHelper('Db')->getTableName('users', false);
        $collection->where('{{project}}.is_deleted=?', '0');

        $collection->joinLeft(
            ['project_manager_user' => $userTable],
            "project_manager_user.ID={{project}}.project_manager_id",
            ['project_manager_name' => 'user_login']
        )->joinLeft(
            ['foreman_user' => $userTable],
            "foreman_user.ID={{project}}.assigned_foreman_id",
            ['foreman_name' => 'user_login']
        )->joinLeft(
            ['created_by_user' => $userTable],
            "created_by_user.ID={{project}}.created_by_id",
            ['created_by_name' => 'user_login']
        )->joinLeft(
            "{{client}}",
            "{{client}}.client_id={{project}}.client_id",
            ['client_name' => 'client_name']
        );

        $collection->order(new \Zend_Db_Expr(sprintf(
                "FIELD({{project}}.status,'%s', '%s', '%s')",
                ProjectDefinition::STATUS_OPEN,
                ProjectDefinition::STATUS_COMPLETE,
                ProjectDefinition::STATUS_CANCELLED)
        ))->order('{{project}}.date_required');

        return $collection;
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Project\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getOpenProjects()
    {
        return $this->getProjectCollection()->where('status=?', ProjectDefinition::STATUS_OPEN);
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
        if(is_object($quoteId)){
            $quoteId = $quoteId->getId();
        }

        if($quoteId) {
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
     * @return \DaveBaker\Core\Model\Db\Base|null|\SuttonBaker\Impresario\Model\Db\Project|null
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     * @throws \Exception
     */
    public function createProjectFromQuote($quoteId)
    {
        $quote = $this->getQuoteHelper()->getQuote($quoteId);

        if(!$quote->getId()){
            return null;
        }

        $project = $this->getProjectForQuote($quoteId);

        if($project->getId()){
            throw new \Exception("Project has already been created for quote {$quoteId}");
        }

        $project = $this->getProject();

        foreach($this->quoteDataValues as $key){
            $project->setData($key, $quote->getData($key));
        }

        $currentUserId = $this->getUserHelper()->getCurrentUserId();

        $project->setLastEditedById($currentUserId)
            ->setCreatedById($currentUserId)
            ->setStatus(QuoteDefinition::STATUS_OPEN);

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

        if($entityId){
            $entity->load($entityId);
        }

        return $entity;
    }


    /**
    * @param \SuttonBaker\Impresario\Model\Db\Project\ $project
    * @throws \DaveBaker\Core\Db\Exception
    * @throws \DaveBaker\Core\Event\Exception
    * @throws \DaveBaker\Core\Object\Exception
    */
    public function deleteProject(
        \SuttonBaker\Impresario\Model\Db\Project $project
    ) {

        // TODO: DELETE VARIATIONS HERE
        $project->setIsDeleted(1)->save();
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