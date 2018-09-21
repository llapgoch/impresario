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
        $instanceCollection = $this->getQuoteHelper()->getDisplayQuotes()
            ->addOutputProcessors([
                'date_received' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'target_date' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'status' => $this->getQuoteHelper()->getStatusOutputProcessor(),
                'tender_status' => $this->getQuoteHelper()->getTenderStatusOutputProcessor(),
                'edit_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getEditLinkHtml']),
            ]);

        $mainTile = $this->getBlockManager()->getBlock("{$this->getBlockPrefix()}.tile.main");
        $mainTile->addChildBlock(
        /** @var Paginator $paginator */
            $paginator = $this->createBlock(
                '\DaveBaker\Core\Block\Components\Paginator',
                "{$this->getBlockPrefix()}.list.paginator",
                'footer'
            )->setRecordsPerPage(QuoteDefinition::RECORDS_PER_PAGE)
                ->setTotalRecords(count($instanceCollection->getItems()))
                ->setIsReplacerBlock(true)
        );

        $this->addChildBlock(
            $tableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Table\StatusLink',
                "{$this->getBlockPrefix()}.list.table"
            )->setHeaders(QuoteDefinition::TABLE_HEADERS)->setRecords($instanceCollection)
                ->setStatusKey('tender_status')
                ->setSortableColumns(QuoteDefinition::SORTABLE_COLUMNS)
                ->setRowStatusClasses(QuoteDefinition::getRowClasses())
                ->addJsDataItems([
                    TableDefinition::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                        $this->getUrlHelper()->getApiUrl(QuoteDefinition::API_ENDPOINT_UPDATE_TABLE)
                ])
                ->setPaginator($paginator)
        );

        if(!count($instanceCollection->getItems())){
            $this->addChildBlock($this->getNoItemsBlock());
        }

        $tableBlock->setLinkCallback(
            function ($headerKey, $record) {
                return $this->getPageUrl(
                    \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT,
                    ['quote_id' => $record->getId()]
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
        return PageDefinition::QUOTE_EDIT;
    }

}
