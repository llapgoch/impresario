<?php

namespace SuttonBaker\Impresario\Block\Invoice;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Invoice as InvoiceDefinition;
/**
 * Class TableContainer
 * @package SuttonBaker\Impresario\Block\Task
 */
class TableContainer
    extends \SuttonBaker\Impresario\Block\Table\Container\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    /** @var string  */
    protected $blockPrefix = 'invoice_table';
    /** @var string  */
    protected $idParam = 'invoice_id';
    /** @var \SuttonBaker\Impresario\Model\Db\Invoice\Collection $instanceCollection */
    protected $instanceCollection;
    /** @var string  */
    protected $tileDefinitionClass = '\SuttonBaker\Impresario\Block\Core\Tile\White';

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Invoice\Collection
     */
    public function getInstanceCollection()
    {
        return $this->instanceCollection;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Invoice\Collection $instanceCollection
     * @return $this
     */
    public function setInstanceCollection(
        \SuttonBaker\Impresario\Model\Db\Invoice\Collection $instanceCollection
    ) {
        $this->instanceCollection = $instanceCollection;
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

        if(!$this->instanceCollection){
            $this->instanceCollection = $this->getInvoiceHelper()->getInvoiceCollection();
        }

        $this->instanceCollection->addOutputProcessors([
            'invoice_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'value' => $this->getLocaleHelper()->getOutputProcessorCurrency()
        ]);

        $instanceItems = $this->instanceCollection->load();

        $this->addChildBlock(
            $tileBlock = $this->createBlock(
                $this->getTileDefinitionClass(),
                'invoice.tile.block'
            )->setHeading('<strong>Invoices</strong>')
        );


        if(count($instanceItems)) {
            $tileBlock->setTileBodyClass('nopadding');

            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\SuttonBaker\Impresario\Block\Table\StatusLink',
                    "invoice.list.table",
                    'content'
                )->setHeaders(InvoiceDefinition::TABLE_HEADERS)
                    ->setRecords($this->instanceCollection)
                    ->addClass('table-striped')
            );

            $tableBlock->setLinkCallback(
                function ($headerKey, $record) {
                    return $this->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::INVOICE_EDIT,
                        ['invoice_id' => $record->getId()],
                        true
                    );
                }
            );
        }else{
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\DaveBaker\Core\Block\Html\Tag',
                    "invoice.list.no.records",
                    'content'
                )->setTagText('No invoices have currently been created')
            );
        }
    }
}