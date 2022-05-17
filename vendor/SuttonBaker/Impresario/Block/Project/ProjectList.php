<?php

namespace SuttonBaker\Impresario\Block\Project;

use DaveBaker\Core\Block\Exception;
use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
use \DaveBaker\Core\Definitions\Table as TableDefinition;
use SuttonBaker\Impresario\Model\Db\Project\Collection;

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

    /** @var Collection */
    protected $instanceCollection;
    /** @var array  */
    protected $tableHeaders = ProjectDefinition::TABLE_HEADERS;
    /** @var array  */
    protected $sortableColumns = ProjectDefinition::SORTABLE_COLUMNS;
    /** @var string */
    protected $endpointUrl = ProjectDefinition::API_ENDPOINT_UPDATE_TABLE;
    /** @var array|bool  */
    protected $rowClasses = [];
    /** @var \DaveBaker\Core\Block\Components\Paginator */
    protected $paginator;
    /** @var \SuttonBaker\Impresario\Block\Table\StatusLink */
    protected $tableBlock;

    /**
     * @return Collection
     */
    public function getInstanceCollection()
    {
        return $this->instanceCollection;
    }

    /**
     * @param Collection $instanceCollection
     * @return $this
     */
    public function setInstanceCollection($instanceCollection)
    {
        $this->instanceCollection = $instanceCollection;
        return $this;
    }

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

        if (!($instanceCollection = $this->getInstanceCollection())) {
            throw new Exception('Instance collection not set');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Project\Collection $enquiryCollection */
        $instanceCollection->addOutputProcessors([
            'date_received' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'target_date' => $this->getDateHelper()->getOutputProcessorFullDate(),
            'status' => $this->getProjectHelper()->getStatusOutputProcessor(),
            'invoice_amount_remaining' => $this->getLocaleHelper()->getOutputProcessorCurrency(),
            'total_net_sell' => $this->getLocaleHelper()->getOutputProcessorCurrency()
        ]);


        $mainTile = $this->getBlockManager()->getBlock("{$this->getBlockPrefix()}.tile.main");
        $mainTile->addChildBlock(
            /** @var Paginator $paginator */
            $this->paginator = $this->createBlock(
                '\DaveBaker\Core\Block\Components\Paginator',
                "{$this->getBlockPrefix()}.list.paginator",
                'footer'
            )->setRecordsPerPage(ProjectDefinition::RECORDS_PER_PAGE)
                ->setIsReplacerBlock(true)
        );

        $this->addChildBlock(
            $this->tableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Table\StatusLink',
                "{$this->getBlockPrefix()}.list.table"
            )->setHeaders($this->getTableHeaders())
                ->setRecords($instanceCollection)
                ->setStatusKey('status')
                ->setRowStatusClasses($this->getRowClasses())
                ->setSortableColumns($this->getSortableColumns())
                ->setShowEmptyTable(true)
                ->addJsDataItems([
                    TableDefinition::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                    $this->getUrlHelper()->getApiUrl($this->getEndpointUrl())
                ])
                ->addClass('js-project-table')
                ->setPaginator($this->paginator)
                ->setFilterSchema(
                    \SuttonBaker\Impresario\Definition\Project::FILTER_LISTING
                )
        );

        if ($this->rowClasses === false) {
            $this->tableBlock->addClass('table-striped');
        }

        $this->tableBlock->setLinkCallback(
            function ($headerKey, $record) {
                return $this->getPageUrl(
                    \SuttonBaker\Impresario\Definition\Page::PROJECT_EDIT,
                    ['project_id' => $record->getId()]
                );
            }
        );
        
        /** @var \SuttonBaker\Impresario\Block\Form\Filter\Set $filterBlock */
        $filterBlock = $this->getBlockManager()->getBlock("{$this->getBlockPrefix()}.filter.set");
        
        $this->tableBlock->preDispatch();

        if (($sessionData = $this->tableBlock->getSessionData())
            && isset($sessionData['filters'])
        ) {
            foreach ($sessionData['filters'] as $filterKey => $filterValue) {
                $filterBlock->setFilterValue($filterKey, $filterValue);
            }
        }
        
    }

    protected function _preRender()
    {
        $this->tableBlock->unpackSession();
        $hiddenClass = $this->getElementConfig()->getConfigValue('hiddenClass');
        $this->tableBlock->setRecords($this->instanceCollection);
        $this->applyRecordCountToPaginator();
        
        $this->addChildBlock(
            $noItemsBlock = $this->getNoItemsBlock("{$this->getBlockPrefix()}.list.table.noitems")
        );
        $noItemsBlock->setIsReplacerBlock(true);

        if (!count($this->instanceCollection->getItems())) {
            $this->paginator->addClass($hiddenClass);
            $this->tableBlock->addClass($hiddenClass);
        } else {
            $noItemsBlock->addClass($hiddenClass);
        }
    }

    /**
     * Method to allow the resetting of paginator values when using the API
     *
     * @return void
     */
    public function applyRecordCountToPaginator()
    {
        $this->paginator
            ->setTotalRecords(count($this->instanceCollection->getItems()));
        return $this;
    }

    /**
     * @return array
     */
    public function getTableHeaders()
    {
        return $this->tableHeaders;
    }

    /**
     * @param array $tableHeaders
     * @return $this
     */
    public function setTableHeaders($tableHeaders)
    {
        $this->tableHeaders = $tableHeaders;
        return $this;
    }

    /**
     * @param array|bool $rowClasses
     * @return $this
     */
    public function setRowClasses($rowClasses)
    {
        $this->rowClasses = $rowClasses;
        return $this;
    }

    /**
     * @return array
     */
    protected function getRowClasses()
    {
        if ($this->rowClasses === false) {
            return [];
        }

        if ($this->rowClasses) {
            return $this->rowClasses;
        }

        return ProjectDefinition::getRowClasses();
    }

    /**
     * @return array
     */
    public function getSortableColumns()
    {
        return $this->sortableColumns;
    }

    /**
     * @param array $sortableColumns
     * @return $this
     */
    public function setSortableColumns($sortableColumns)
    {
        $this->sortableColumns = $sortableColumns;
        return $this;
    }

    /**
     * @return string
     */
    public function getEndpointUrl()
    {
        return $this->endpointUrl;
    }

    /**
     * @param string $endpointUrl
     * @return $this
     */
    public function setEndpointUrl($endpointUrl)
    {
        $this->endpointUrl = $endpointUrl;
        return $this;
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
