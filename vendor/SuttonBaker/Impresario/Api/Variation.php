<?php

namespace SuttonBaker\Impresario\Api;

use \DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Definition\Variation as VariationDefinition;

/**
 * Class Variation
 * @package SuttonBaker\Impresario\Api
 */
class Variation
    extends Base
{
    /** @var string */
    protected $blockPrefix = 'invoice';
    /** @var array */
    protected $capabilities = [Roles::CAP_VIEW_VARIATION];

    /**
     * @param $params
     * @param WP_REST_Request $request
     * @return bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function deleteAction($params, \WP_REST_Request $request)
    {
        /** @var \SuttonBaker\Impresario\Helper\Variation $helper */
        $helper = $this->createAppObject('\SuttonBaker\Impresario\Helper\Variation');

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['id'])){
            throw new Exception('The item could not be found');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Invoice $item */
        $item = $this->createAppObject(
            VariationDefinition::DEFINITION_MODEL
        )->load($params['id']);

        if(!$item->getId()){
            throw new Exception('The variation could not be found');
        }

        $helper->deleteVariation($item);
        $this->addMessage('The variation has been removed', Messages::SUCCESS);

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
            throw new Exception('ID is required');
        }
        
        $object = $this->getVariationHelper()->getVariation($params['id']);
        return $this->performRecordMonitor($params, $object);
    }

}