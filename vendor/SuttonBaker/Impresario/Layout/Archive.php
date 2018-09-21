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
                "project.tile.main")
                ->setHeading("<strong>Archived</strong> Projects")
                ->setShortcode('body_content')
                ->setTileBodyClass('nopadding')
        );

        $instanceCollection = $this->getProjectHelper()->getProjectCollection()
            ->where('status=?', ProjectDefinition::STATUS_COMPLETE);

        $instanceCollection->addOutputProcessors([
            'profit' => $this->getLocaleHelper()->getOutputProcessorCurrency(),
            'total_net_cost' => $this->getLocaleHelper()->getOutputProcessorCurrency(),
            'total_net_sell' => $this->getLocaleHelper()->getOutputProcessorCurrency()
        ]);


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
    }
}
