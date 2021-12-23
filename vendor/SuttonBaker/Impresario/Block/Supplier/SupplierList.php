<?php

namespace SuttonBaker\Impresario\Block\Supplier;

use DaveBaker\Core\Definitions\Table;
use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Supplier as SupplierDefinition;
/**
 * Class SupplierList
 * @package SuttonBaker\Impresario\Block\Supplier
 */
class SupplierList
    extends \SuttonBaker\Impresario\Block\ListBase
    implements \DaveBaker\Core\Block\BlockInterface
{
    const BLOCK_PREFIX = 'supplier';
    const ID_PARAM = 'supplier_id';

    /**
     * @return \DaveBaker\Core\Block\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        wp_enqueue_script('dbwpcore_table_updater');

        $tableHeaders = SupplierDefinition::TABLE_HEADERS;

        /** @var \SuttonBaker\Impresario\Model\Db\Supplier\Collection $enquiryCollection */
        $instanceCollection = $this->getSupplierHelper()->getSupplierCollection();

        // Do this check, as we won't have the maintile when reloading the table with ajax
        if($mainTile = $this->getBlockManager()->getBlock('supplier.tile.main')) {
            $mainTile->addChildBlock(
            /** @var Paginator $paginator */
                $paginator = $this->createBlock(
                    '\DaveBaker\Core\Block\Components\Paginator',
                    'supplier.list.paginator',
                    'footer'
                )->setOrder('after', 'supplier.list.table')
                    ->setRecordsPerPage(SupplierDefinition::RECORDS_PER_PAGE)
                    ->setTotalRecords(count($instanceCollection->getItems()))
                    ->setIsReplacerBlock(true)
            );
        }

        $this->addChildBlock(
            $tableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Table\StatusLink',
                'supplier.list.table'
            )->setHeaders($tableHeaders)->setRecords($instanceCollection)
                ->addClass('table-striped js-table-updater')
                ->addSortableColumns(SupplierDefinition::SORTABLE_COLUMNS)
                ->addJsDataItems([
                    Table::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                    $this->getUrlHelper()->getApiUrl(SupplierDefinition::API_ENDPOINT_UPDATE_TABLE)
                ])->setPaginator($paginator)
        );

        if(!count($instanceCollection->getItems())){
            $this->addChildBlock($this->getNoItemsBlock());
        }

        $tableBlock->setLinkCallback(
            function ($headerKey, $record) {
                return $this->getPageUrl(
                    \SuttonBaker\Impresario\Definition\Page::SUPPLIER_EDIT,
                    ['supplier_id' => $record->getId()]
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
        return PageDefinition::SUPPLIER_EDIT;
    }
}
