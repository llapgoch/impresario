<?php

namespace SuttonBaker\Impresario\Block\Quote;

use DaveBaker\Core\Block\Exception;
use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Definition\Quote;

/**
 * Class TableContainer
 * @package SuttonBaker\Impresario\Block\Quote
 */
class RevisionsTableContainer
    extends \SuttonBaker\Impresario\Block\Table\Container\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    /** @var string  */
    protected $blockPrefix = 'quote.revision.table';
    /** @var string  */
    protected $tileDefinitionClass = '\SuttonBaker\Impresario\Block\Core\Tile\White';
    /** @var \SuttonBaker\Impresario\Model\Db\Quote */
    protected $parentQuote;

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Quote
     */
    public function getParentQuote()
    {
        return $this->parentQuote;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $parentQuote
     * @return $this
     */
    public function setParentQuote($parentQuote)
    {
        $this->parentQuote = $parentQuote;
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

        if(!$this->getParentQuote()){
            throw new Exception('Quote Revision Parent not set');
        }

        $instanceCollection = $this->getParentQuote()->getPastRevisions();

        $this->addChildBlock(
            $tileBlock = $this->createBlock(
                $this->getTileDefinitionClass(),
                "{$this->getBlockPrefix()}.tile.block"
            )->setHeading('<strong>Past</strong> Revisions')
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
        );

        if(count($instanceCollection->getItems())) {
            $tileBlock->setTileBodyClass('nopadding');

            /** \SuttonBaker\Impresario\Block\Table\StatusLink $tableBlock */
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\SuttonBaker\Impresario\Block\Table\StatusLink',
                    "{$this->getBlockPrefix()}.list.table",
                    'content'
                )->setHeaders(Quote::TABLE_HEADERS)
                    ->setRecords($instanceCollection)
                    ->setSortableColumns(Quote::SORTABLE_COLUMNS)
                    ->addJsDataItems([
                        Table::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                            $this->getUrlHelper()->getApiUrl(Quote::API_ENDPOINT_UPDATE_REVISIONS_TABLE)
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