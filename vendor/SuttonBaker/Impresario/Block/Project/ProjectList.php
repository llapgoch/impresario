<?php

namespace SuttonBaker\Impresario\Block\Project;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
use \DaveBaker\Core\Definitions\Table as TableDefinition;
/**
 * Class ProjectList
 * @package SuttonBaker\Impresario\Block\Project
 */
class ProjectList
    extends \SuttonBaker\Impresario\Block\ListBase
    implements \DaveBaker\Core\Block\BlockInterface
{
    const BLOCK_PREFIX = 'project';
    const ID_PARAM = 'project_id';

    /**
     * @return \SuttonBaker\Impresario\Block\ListBase|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function _preDispatch()
    {
        wp_enqueue_script('dbwpcore_table_updater');

        /** @var \SuttonBaker\Impresario\Model\Db\Project\Collection $enquiryCollection */
        $instanceItems = $this->getProjectHelper()->getProjectCollection()
            ->addOutputProcessors([
                'date_received' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'target_date' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'status' => $this->getProjectHelper()->getStatusOutputProcessor()
            ]);


        $mainTile = $this->getBlockManager()->getBlock("{$this->getBlockPrefix()}.tile.main");
        $mainTile->addChildBlock(
        /** @var Paginator $paginator */
            $paginator = $this->createBlock(
                '\DaveBaker\Core\Block\Components\Paginator',
                "{$this->getBlockPrefix()}.list.paginator",
                'footer'
            )->setRecordsPerPage(ProjectDefinition::RECORDS_PER_PAGE)
                ->setTotalRecords(count($instanceItems->getItems()))
                ->setIsReplacerBlock(true)
        );

        $this->addChildBlock(
            $tableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Table\StatusLink',
                "{$this->getBlockPrefix()}.list.table"
            )->setHeaders(ProjectDefinition::TABLE_HEADERS)->setRecords($instanceItems)
                ->setStatusKey('status')
                ->setRowStatusClasses(ProjectDefinition::getRowClasses())
                ->setSortableColumns(ProjectDefinition::SORTABLE_COLUMNS)
                ->addJsDataItems([
                    TableDefinition::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                        $this->getUrlHelper()->getApiUrl(ProjectDefinition::API_ENDPOINT_UPDATE_TABLE)
                ])
                ->setPaginator($paginator)
        );

        $tableBlock->setLinkCallback(
            function ($headerKey, $record) {
                return $this->getPageUrl(
                    \SuttonBaker\Impresario\Definition\Page::PROJECT_EDIT,
                    ['project_id' => $record->getId()]
                );
            }
        );
    }

    /**
     * @return string
     */
    protected function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }

    /**
     * @return string
     */
    protected function getInstanceIdParam()
    {
        return self::ID_PARAM;
    }

    /**
     * @return string
     */
    protected function getEditPageIdentifier()
    {
        return PageDefinition::PROJECT_EDIT;
    }

}
