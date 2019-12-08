<?php

namespace SuttonBaker\Impresario\Layout;

use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
use \SuttonBaker\Impresario\Definition\Archive as ArchiveDefinition;

/**
 * Class Client
 * @package SuttonBaker\Impresario\Layout
 */
class Archive extends Base
{
    /** @var string */
    protected $blockPrefix = 'archive';
    protected $headingName = 'Archive';
    protected $icon = \SuttonBaker\Impresario\Definition\Archive::ICON;

    public function archiveListHandle()
    {
        $this->addHeading()->addMessages();

        $this->addBlock(
            /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "project.tile.main"
            )
                ->setHeading("<strong>Archived</strong> Projects")
                ->setShortcode('body_content')
                ->setTileBodyClass('nopadding table-responsive')
        );

        $instanceCollection = $this->getProjectHelper()->getProjectCollection()
            ->where('status=?', ProjectDefinition::STATUS_COMPLETE);

        $instanceCollection->addOutputProcessors([
            'profit' => $this->getLocaleHelper()->getOutputProcessorCurrency(),
            'total_net_cost' => $this->getLocaleHelper()->getOutputProcessorCurrency(),
            'total_net_sell' => $this->getLocaleHelper()->getOutputProcessorCurrency(),
            'net_sell' => $this->getLocaleHelper()->getOutputProcessorCurrency()
        ]);

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
                        \SuttonBaker\Impresario\Definition\Page::ARCHIVE_REPORT_DOWNLOAD
                    )]
                )->setCapabilities($this->getProjectHelper()->getViewCapabilities())
        );


        $mainTile->addChildBlock(
            $mainTile->createBlock(
                '\SuttonBaker\Impresario\Block\Project\ProjectList',
                "project.list",
                'content'
            )->setInstanceCollection($instanceCollection)
                ->setRowClasses(false)
                ->setTableHeaders(ArchiveDefinition::TABLE_HEADERS)
                ->setSortableColumns(ArchiveDefinition::SORTABLE_COLUMNS)
                ->setEndpointUrl(ArchiveDefinition::API_ENDPOINT_UPDATE_TABLE)
        );
        
        $this->createFilterSet($mainTile);
        return $this;
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
                "project.filter.set",
                'controls'
            )->setCapabilities($this->getEnquiryHelper()->getViewCapabilities())
                ->setSetName('project_filters')
                ->addClass('js-project-filters')
                ->addJsDataItems([
                    'tableUpdaterSelector' => '.js-project-table'
                ])
        );

        $filterSet->addFilter(
            $filterSet->createBlock(\SuttonBaker\Impresario\Block\Form\Filter\Text::class)
                ->setLabelName('ID')
                ->setFormName('project_id')
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

        return $this;
    }
}
