<?php

namespace SuttonBaker\Impresario\Block\Cost;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
/**
 * Class TableContainer
 * @package SuttonBaker\Impresario\Block\Task
 */
class TableContainer
    extends \SuttonBaker\Impresario\Block\Table\Container\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    /** @var string  */
    protected $blockPrefix = 'cost_table';
    /** @var string  */
    protected $idParam = 'cost_id';
    /** @var \SuttonBaker\Impresario\Model\Db\Cost\Collection $instanceCollection */
    protected $instanceCollection;
    /** @var string  */
    protected $tileDefinitionClass = '\SuttonBaker\Impresario\Block\Core\Tile\White';

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Cost\Collection
     */
    public function getInstanceCollection()
    {
        return $this->instanceCollection;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Cost\Collection $instanceCollection
     * @return $this
     */
    public function setInstanceCollection(
        \SuttonBaker\Impresario\Model\Db\Cost\Collection $instanceCollection
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
            $this->instanceCollection = $this->getCostHelper()->getCostCollection();
        }

        $this->instanceCollection->addOutputProcessors([
            'cost_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'po_item_total' => $this->getLocaleHelper()->getOutputProcessorCurrency(),
            'cost_invoice_type' => $this->getCostHelper()->getCostInvoiceTypeOutputProcessor(),
            'status' => $this->getCostHelper()->getCostStatusOutputProcessor()
        ]);

        $instanceItems = $this->instanceCollection->load();

        $this->addChildBlock(
            $tileBlock = $this->createBlock(
                $this->getTileDefinitionClass(),
                'cost.tile.block'
            )->setHeading('<strong>Purchase Orders</strong>')
        );


        if(count($instanceItems)) {
            $tileBlock->setTileBodyClass('nopadding table-responsive');

            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\SuttonBaker\Impresario\Block\Table\StatusLink',
                    "cost.list.table",
                    'content'
                )->setHeaders(CostDefinition::TABLE_HEADERS)
                    ->setRecords($this->instanceCollection)
                    ->addClass('table-striped')
            );

            $tableBlock->setLinkCallback(
                function ($headerKey, $record) {
                    return $this->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::COST_EDIT,
                        ['cost_id' => $record->getId()],
                        true
                    );
                }
            );
        }else{
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\DaveBaker\Core\Block\Html\Tag',
                    "cost.list.no.records",
                    'content'
                )->setTagText('No purchase orders have currently been created')
            );
        }
    }
}