<?php

namespace SuttonBaker\Impresario\Block\Cost\Item;

use Exception;
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
    protected $blockPrefix = 'cost_item_table';

    /** @var \SuttonBaker\Impresario\Model\Db\Cost\Item\Collection $instanceCollection */
    protected $instanceCollection;
    /** @var string  */
    protected $tileDefinitionClass = '\SuttonBaker\Impresario\Block\Core\Tile\White';

    protected bool $locked = true;

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Cost\Collection
     */
    public function getInstanceCollection()
    {
        return $this->instanceCollection;
    }

    public function setLocked(bool $locked)
    {
        $this->locked = $locked;
        return $this;
    }

    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Cost\Item\Collection $instanceCollection
     * @return $this
     */
    public function setInstanceCollection(
        \SuttonBaker\Impresario\Model\Db\Cost\Item\Collection $instanceCollection
    ) {
        $this->instanceCollection = $instanceCollection;
        return $this;
    }

    public function formatTo2DP($value)
    {
        return round((float) $value, 2);
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
        if (!$this->instanceCollection) {
            throw new Exception('Instance collection not defined');
        }

        $this->instanceCollection->addOutputProcessors([
            'unit_price' => $this->getCustomOutputProcessor()->setCallback([$this, 'formatTo2DP'])
        ]);

        $this->instanceCollection->load();

        $this->addChildBlock(
            $tileBlock = $this->createBlock(
                $this->getTileDefinitionClass(),
                'cost.item.tile.block'
            )->setHeading('<strong>PO Items</strong>')
        );

        if (!$this->getLocked()) {
            $addButton = $this->createBlock(
                \DaveBaker\Form\Block\Button::class,
                'create.cost.item.button',
                'header_elements'
            )->setButtonName('Add Item')
                ->addAttribute(['type' =>  'button'])
                ->addClass('btn btn-sm btn-primary js-po-item-create');

            $tileBlock->addChildBlock($addButton);
        }

        $tileBlock->setTileBodyClass('nopadding table-responsive');

        $tileBlock->addChildBlock(
            $tableBlock = $tileBlock->createBlock(
                \SuttonBaker\Impresario\Block\Table\StatusLink::class,
                "cost.item.list.table",
                'content'
            )->setHeaders(CostDefinition::ITEM_TABLE_HEADERS)
                ->setRecords($this->instanceCollection)
                ->addClass('table-striped js-po-item-table')
                ->setTemplate('html/table/cost/status-link-attribute-value.phtml')
                // We can't use addJsDataItems without setting jsUpdater to false, but we need jsItems for the cost js
                ->addJsDataItems(['locked' => $this->getLocked()])
                ->setJsUpdater(false)
        );
    }
}
