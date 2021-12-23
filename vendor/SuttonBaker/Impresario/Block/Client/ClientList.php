<?php

namespace SuttonBaker\Impresario\Block\Client;

use DaveBaker\Core\Block\Components\Paginator;
use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Definition\Client;
use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Client as ClientDefinition;
/**
 * Class ClientList
 * @package SuttonBaker\Impresario\Block\Client
 */
class ClientList
    extends \SuttonBaker\Impresario\Block\ListBase
    implements \DaveBaker\Core\Block\BlockInterface
{
    const BLOCK_PREFIX = 'client';
    const ID_PARAM = 'client_id';

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

        $tableHeaders = ClientDefinition::TABLE_HEADERS;

        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $enquiryCollection */
        $instanceCollection = $this->getClientHelper()->getClientCollection();

        // Do this check, as we won't have the maintile when reloading the table with ajax
        if($mainTile = $this->getBlockManager()->getBlock('client.tile.main')) {
            $mainTile->addChildBlock(
            /** @var Paginator $paginator */
                $paginator = $this->createBlock(
                    '\DaveBaker\Core\Block\Components\Paginator',
                    'client.list.paginator',
                    'footer'
                )->setOrder('after', 'client.list.table')
                    ->setRecordsPerPage(ClientDefinition::RECORDS_PER_PAGE)
                    ->setTotalRecords(count($instanceCollection->getItems()))
                    ->setIsReplacerBlock(true)
            );
        }

        $this->addChildBlock(
            $tableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Table\StatusLink',
                'client.list.table'
            )->setHeaders($tableHeaders)->setRecords($instanceCollection)
                ->addClass('table-striped js-table-updater')
                ->addSortableColumns(ClientDefinition::SORTABLE_COLUMNS)
                ->addJsDataItems([
                    Table::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                    $this->getUrlHelper()->getApiUrl(ClientDefinition::API_ENDPOINT_UPDATE_TABLE)
                ])->setPaginator($paginator)
        );

        if(!count($instanceCollection->getItems())){
            $this->addChildBlock($this->getNoItemsBlock());
        }

        $tableBlock->setLinkCallback(
            function ($headerKey, $record) {
                return $this->getPageUrl(
                    \SuttonBaker\Impresario\Definition\Page::CLIENT_EDIT,
                    ['client_id' => $record->getId()]
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
        return PageDefinition::CLIENT_EDIT;
    }
}
