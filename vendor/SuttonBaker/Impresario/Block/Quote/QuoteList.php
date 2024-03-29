<?php

namespace SuttonBaker\Impresario\Block\Quote;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use \DaveBaker\Core\Definitions\Table as TableDefinition;

/**
 * Class QuoteList
 * @package SuttonBaker\Impresario\Block\Quote
 */
class QuoteList
extends \SuttonBaker\Impresario\Block\ListBase
implements \DaveBaker\Core\Block\BlockInterface
{
    const BLOCK_PREFIX = 'quote';
    const COMPLETED_KEY = 'completed';
    const ID_PARAM = 'quote_id';

    /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $instanceCollection */
    protected $instanceCollection;
    /** @var \DaveBaker\Core\Block\Components\Paginator */
    protected $paginator;
    /** @var \SuttonBaker\Impresario\Block\Table\StatusLink */
    protected $tableBlock;

    /**
     * @return \SuttonBaker\Impresario\Block\ListBase|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    protected function _preDispatch()
    {
        wp_enqueue_script('dbwpcore_table_updater');

        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $instanceCollection */
        $this->instanceCollection = $this->getQuoteHelper()->getDisplayQuotes()
            ->addOutputProcessors([
                'date_required' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'target_date' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'status' => $this->getQuoteHelper()->getStatusOutputProcessor(),
                'tender_status' => $this->getQuoteHelper()->getTenderStatusOutputProcessor(),
                'edit_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getEditLinkHtml']),
                'revision_number' => $this->getQuoteHelper()->getRevisionOutputProcessor(),
                'net_sell' => $this->getLocaleHelper()->getOutputProcessorCurrency(),
                'priority' => $this->getQuoteHelper()->getPriorityOutputProcessor()
            ]);

        $mainTile = $this->getBlockManager()->getBlock("{$this->getBlockPrefix()}.tile.main");
        $mainTile->addChildBlock(
            /** @var Paginator $paginator */
            $this->paginator = $this->createBlock(
                '\DaveBaker\Core\Block\Components\Paginator',
                "{$this->getBlockPrefix()}.list.paginator",
                'footer'
            )->setRecordsPerPage(QuoteDefinition::RECORDS_PER_PAGE)
                ->setIsReplacerBlock(true)
        );

        $this->addChildBlock(
            $this->tableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Table\StatusLink',
                "{$this->getBlockPrefix()}.list.table"
            )->setHeaders(QuoteDefinition::TABLE_HEADERS)->setRecords($this->instanceCollection)
                ->setSortableColumns(QuoteDefinition::SORTABLE_COLUMNS)
                ->addJsDataItems([
                    TableDefinition::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                    $this->getUrlHelper()->getApiUrl(QuoteDefinition::API_ENDPOINT_UPDATE_TABLE)
                ])
                ->setPaginator($this->paginator)
                ->setShowEmptyTable(true)
                ->addClass('js-quote-table')
                ->setRowClassCallback(function ($record) {
                    return QuoteDefinition::getRowClass($record);
                })->setFilterSchema(
                    \SuttonBaker\Impresario\Definition\Quote::FILTER_LISTING
                )
        );

        $this->tableBlock->setLinkCallback(
            function ($headerKey, $record) {
                return $this->getPageUrl(
                    \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT,
                    ['quote_id' => $record->getId()]
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
        return PageDefinition::QUOTE_EDIT;
    }
}
