<?php

namespace SuttonBaker\Impresario\Api;

use \DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Definition\Invoice as InvoiceDefinition;

/**
 * Class Invoice
 * @package SuttonBaker\Impresario\Api
 */
class Invoice
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
        /** @var \SuttonBaker\Impresario\Helper\Invoice $helper */
        $helper = $this->createAppObject('\SuttonBaker\Impresario\Helper\Invoice');

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['id'])){
            throw new Exception('The item could not be found');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Invoice $item */
        $item = $this->createAppObject(
            InvoiceDefinition::DEFINITION_MODEL
        )->load($params['id']);

        if(!$item->getId()){
            throw new Exception('The invoice could not be found');
        }

        $helper->deleteInvoice($item);
        $this->addMessage('The invoice has been removed', Messages::SUCCESS);

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
        
        $object = $this->getInvoiceHelper()->getInvoice($params['id']);
        return $this->performRecordMonitor($params, $object);
    }

}