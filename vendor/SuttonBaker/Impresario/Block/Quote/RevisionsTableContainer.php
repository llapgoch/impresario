<?php

namespace SuttonBaker\Impresario\Block\Quote;

use DaveBaker\Core\Block\Exception;
use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Definition\Quote;
use SuttonBaker\Impresario\Model\Db\Quote\Collection;

/**
 * Class TableContainer
 * @package SuttonBaker\Impresario\Block\Quote
 */
class RevisionsTableContainer
    extends \SuttonBaker\Impresario\Block\Table\Container\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    /** @var string  */
    protected $blockPrefix = 'quote.revision';
    /** @var string  */
    protected $tileDefinitionClass = '\SuttonBaker\Impresario\Block\Core\Tile\White';
    /** @var Collection */
    protected $revisions;
    /** @var Quote */
    protected $quote;

    /**
     * @return Quote
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * @param Quote $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getRevisions()
    {
        return $this->revisions;
    }

    /**
     * @param Collection $revisions
     * @return $this
     */
    public function setRevisions($revisions)
    {
        $this->revisions = $revisions;
        return $this;
    }

    /**
     * @return \SuttonBaker\Impresario\Block\Table\Container\Base|void
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

        if(!$this->getQuote() || !($instanceCollection = $this->getRevisions())){
            throw new Exception('Revisions or quote not set');
        }

        $instanceCollection->addOutputProcessors([
            'net_cost' => $this->getLocaleHelper()->getOutputProcessorCurrency(),
            'net_sell' => $this->getLocaleHelper()->getOutputProcessorCurrency(),
            'created_at' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'revision_number' => $this->getQuoteHelper()->getRevisionOutputProcessor()
        ]);

        $this->addChildBlock(
            $tileBlock = $this->createBlock(
                $this->getTileDefinitionClass(),
                "{$this->getBlockPrefix()}.tile.block"
            )->setHeading('<strong>Quote</strong> Revisions')
        );

        $tileBlock->addChildBlock(
        /** @var Paginator $paginator */
            $paginator = $this->createBlock(
                '\DaveBaker\Core\Block\Components\Paginator',
                "{$this->getBlockPrefix()}.list.paginator",
                'footer'
            )->setRecordsPerPage(Quote::RECORDS_PER_PAGE_INLINE)
                ->setTotalRecords(count($instanceCollection->getItems()))
                ->setIsReplacerBlock(true)
                ->addClass('pagination-xs')
        );


        if(count($instanceCollection->getItems())) {
            $tileBlock->setTileBodyClass('nopadding table-responsive');

            /** \SuttonBaker\Impresario\Block\Table\StatusLink $tableBlock */
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\SuttonBaker\Impresario\Block\Table\StatusLink',
                    "{$this->getBlockPrefix()}.list.table",
                    'content'
                )->setHeaders(Quote::TABLE_HEADERS_INLINE)
                    ->setRecords($instanceCollection)
                    ->setSortableColumns(Quote::SORTABLE_COLUMNS)
                    ->addClass('table-striped')
                    ->setStatusKey('tender_status')
                    ->setRowStatusClasses(Quote::getInlineRowClasses())
                    ->addJsDataItems([
                        Table::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                            $this->getUrlHelper()->getApiUrl(
                                Quote::API_ENDPOINT_UPDATE_REVISIONS_TABLE,
                                ['quote_id' => $this->getQuote()->getId()]
                            )
                    ])
                    ->setPaginator($paginator)
            );

            $tableBlock->setLinkCallback(
                function ($headerKey, $record) {
                    return $this->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT,
                        ['quote_id' => $record->getId()],
                        true
                    );
                }
            );
        }else{
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\DaveBaker\Core\Block\Html\Tag',
                    "{$this->getBlockPrefix()}.list.table",
                    'content'
                )->setTagText('There are no past revisions for this quote')
            );
        }
    }
}