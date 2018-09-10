<?php

namespace SuttonBaker\Impresario\Api;
use \SuttonBaker\Impresario\Definition\Client as ClientDefinition;
use DaveBaker\Core\Definitions\Table as TableDefinition;

/**
 * Class Client
 * @package SuttonBaker\Impresario\Api
 *
 */
class Client
    extends \DaveBaker\Core\Api\Base
{

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function updatetableAction($params, \WP_REST_Request $request)
    {
        /** @var \SuttonBaker\Impresario\Helper\Client $clientHelper */
        $clientHelper = $this->createAppObject('\SuttonBaker\Impresario\Helper\Client');
        /** @var \SuttonBaker\Impresario\Model\Db\Client\Collection $instanceItems */
        $instanceItems = $clientHelper->getClientCollection();
        $tableHeaders = ClientDefinition::TABLE_HEADERS;


        /** @var \SuttonBaker\Impresario\Block\Table\StatusLink $tableBlock */
        $tableBlock = $this->getApp()->getBlockManager()->createBlock(
            '\SuttonBaker\Impresario\Block\Table\StatusLink',
            'client.list.table'
        )->setHeaders($tableHeaders)->setRecords($instanceItems)->addEscapeExcludes(
            ['delete_column']
        )->addClass('table-striped js-table-updater')
            ->addSortableColumns(ClientDefinition::SORTABLE_COLUMNS)
            ->addAttribute([
                TableDefinition::ELEMENT_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                    $this->getUrlHelper()->getApiUrl(ClientDefinition::API_ENDPOINT_UPDATE_TABLE)
            ]);


        if(isset($params['order']['dir']) && isset($params['order']['column'])){
            $tableBlock->setColumnOrder($params['order']['column'], $params['order']['dir']);
        }


        $this->addReplacerBlock($tableBlock);
    }

}