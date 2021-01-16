<?php

namespace SuttonBaker\Impresario\Api;

use \DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Definition\Cost as CostDefinition;

/**
 * Class Invoice
 * @package SuttonBaker\Impresario\Api
 */
class Cost
    extends Base
{
    /** @var string */
    protected $blockPrefix = 'cost';
    /** @var array */
    protected $capabilities = [Roles::CAP_ALL, Roles::CAP_VIEW_COST];

    /**
     * @param $params
     * @param WP_REST_Request $request
     * @return bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function deleteAction($params, \WP_REST_Request $request)
    {
        /** @var \SuttonBaker\Impresario\Helper\Cost $helper */
        $helper = $this->getCostHelper();

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['id'])){
            throw new \Exception('The item could not be found');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Cost $item */
        $item = $this->createAppObject(
            CostDefinition::DEFINITION_MODEL
        )->load($params['id']);

        if(!$item->getId()){
            throw new \Exception('The invoice could not be found');
        }

        $helper->deleteCost($item);
        $this->addMessage('The cost invoice has been removed', Messages::SUCCESS);

        return true;
    }

    /**
     * @param array $params
     * @param \WP_REST_Request $request
     * @return array
     */
    public function recordmonitorAction(
        $params,
        \WP_REST_Request $request
    ) {
        if(!isset($params['id'])){
            throw new \Exception('ID is required');
        }
        
        $object = $this->getCostHelper()->getCost($params['id']);
        return $this->performRecordMonitor($params, $object);
    }

}