<?php

namespace SuttonBaker\Impresario\Layout;

use SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
use SuttonBaker\Impresario\Definition\Page as PageDefinition;

/**
 * Class Project
 * @package SuttonBaker\Impresario\Layout
 */
class Project extends Base
{
    const ID_KEY = 'project_id';

    /** @var string  */
    protected $blockPrefix = 'project';
    /** @var string  */
    protected $headingName = 'Projects';
    protected $icon = \SuttonBaker\Impresario\Definition\Project::ICON;

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function projectEditHandle()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Project $entityInstance */
        $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Project');

        if ($entityId = $this->getRequest()->getParam(self::ID_KEY)) {
            $entityInstance->load($entityId);

            if (!$entityInstance->getId()) {
                return;
            }
        }

        $this->addHeading()->addMessages();

        $this->addBlock(
            /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main"
            )
                ->setHeading($this->getProjectHelper()->getActionVerb($entityInstance) . " <strong>Project</strong>")
                ->setShortcode('body_content')
                ->addChildBlock($this->getProjectHelper()->getTabBarForProject($entityInstance))
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Project\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('project_edit_form')

        );
    }


    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function projectListHandle()
    {
        $this->addHeading()->addMessages();

        $this->addBlock(
            /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main"
            )
                ->setHeading("<strong>Project</strong> List")
                ->setShortcode('body_content')
                ->setTileBodyClass('nopadding table-responsive')
        );

        $instanceCollection = $this->getProjectHelper()->getProjectCollection()
            ->where('status<>?', ProjectDefinition::STATUS_COMPLETE);
          

        $mainTile->addChildBlock(
            $buttonContainer = $mainTile->createBlock(
                \DaveBaker\Core\Block\Block::class,
                "{$this->getBlockPrefix()}.button.container",
                'header_elements'
            )
        );


        $buttonContainer->addChildBlock(
            $buttonContainer->createBlock(
                '\DaveBaker\Core\Block\Html\ButtonAnchor',
                "report.{$this->getBlockPrefix()}.download.link"
            )
                ->setTagText('<span class="fa fa-download" aria-hidden="true"></span>')
                ->addAttribute(
                    ['href' => $this->getRequest()->getUrlHelper()->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::PROJECT_REPORT_DOWNLOAD
                    )]
                )->setCapabilities($this->getProjectHelper()->getViewCapabilities())
        );


        $mainTile->addChildBlock(
            $mainTile->createBlock(
                '\SuttonBaker\Impresario\Block\Project\ProjectList',
                "{$this->getBlockPrefix()}.list",
                'content'
            )->setInstanceCollection($instanceCollection)
        );

        $this->createFilterSet($mainTile);
    }

    /**
     * @param string $location
     * @return $this
     */
    public function createFilterSet(
        $location
    ) {
        /** @var \SuttonBaker\Impresario\Block\Form\Filter\Set $filterSet */
        $location->addChildBlock(
            $filterSet = $location->createBlock(
                \SuttonBaker\Impresario\Block\Form\Filter\Set::class,
                "{$this->getBlockPrefix()}.filter.set",
                'controls'
            )->setCapabilities($this->getEnquiryHelper()->getViewCapabilities())
                ->setSetName('project_filters')
                ->addClass('js-project-filters')
                ->addJsDataItems([
                    'tableUpdaterSelector' => '.js-project-table'
                ])
        );

        // Clients
        $clients = $this->createCollectionSelectConnector()
            ->configure(
                $this->getClientHelper()->getClientCollection(),
                'client_id',
                'client_name'
            )->getElementData();

        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Select::class)
                ->setLabelName('Client')
                ->setFormName('client_id')
                ->setSelectOptions($clients)
        );

        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Text::class)
                ->setLabelName('Client Ref')
                ->setFormName('client_reference')
        );

        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Text::class)
                ->setLabelName('Site')
                ->setFormName('site_name')
        );

        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Text::class)
                ->setLabelName('Project')
                ->setFormName('project_name')
        );

        if ($projectManagers = $this->getRoleHelper()->getProjectManagers()) {
            $projectManagers = $this->createCollectionSelectConnector()
                ->configure(
                    $projectManagers,
                    'ID',
                    'display_name'
                )->getElementData();

            $filterSet->addFilter(
                $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Select::class)
                    ->setLabelName('Contracts Manager')
                    ->setFormName('project_manager_id')
                    ->setSelectOptions($projectManagers)
            );
        }

        // Foremen
        if ($foremen = $this->getRoleHelper()->getForemen()) {
            $foremen = $this->createCollectionSelectConnector()
                ->configure(
                    $foremen,
                    'ID',
                    'display_name'
                )->getElementData();

            $filterSet->addFilter(
                $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Select::class)
                    ->setLabelName('Foreman')
                    ->setFormName('assigned_foreman_id')
                    ->setSelectOptions($foremen)
            );
        }

        // Statuses
        $statuses = $this->createArraySelectConnector()->configure(
            ProjectDefinition::getStatuses()
        )->getElementData();

        //TODO: Sort this!
        unset($statuses[\SuttonBaker\Impresario\Definition\Project::STATUS_COMPLETE]);

        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Select::class)
                ->setLabelName('Status')
                ->setFormName('status')
                ->setSelectOptions($statuses)
        );

        return $this;
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function indexHandle()
    {
        $openProjects = count($this->getProjectHelper()->getOpenProjects()->load());
        $totalProjects = count($this->getProjectHelper()->getProjectCollection()->load());

        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\FlipCard',
                'projects.flip.card'
            )->setShortcode('body_content')
                ->setTemplate('core/flip-card.phtml')
                ->setIcon(\SuttonBaker\Impresario\Definition\Project::ICON)
                ->setHeading('Open Projects')
                ->setNumber($openProjects)
                ->setColour('amethyst')
                ->setProgressPercentage($this->getProjectHelper()->getPercentage($openProjects, $totalProjects))
                ->setProgressHeading("{$openProjects} open out of {$totalProjects} total projects")
                ->setBackLink($this->getUrlHelper()->getPageUrl(PageDefinition::PROJECT_LIST))
                ->setBackText('View Projects')
                ->setCapabilities($this->getProjectHelper()->getViewCapabilities())
        );
    }
}
