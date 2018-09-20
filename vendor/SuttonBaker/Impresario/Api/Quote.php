<?php

namespace SuttonBaker\Impresario\Api;
use DaveBaker\Core\Api\Exception;
use DaveBaker\Core\Block\Components\Paginator;
use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Block\Table\StatusLink;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Quote
 * @package SuttonBaker\Impresario\Api
 *
 */
class Quote
    extends \DaveBaker\Core\Api\Base
{
    /** @var string  */
    protected $blockPrefix = 'quote';
    /** @var array  */
    protected $capabilities = [Roles::CAP_VIEW_QUOTE];

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function updatetableAction($params, \WP_REST_Request $request)
    {
        $blockManager = $this->getApp()->getBlockManager();

        /** @var StatusLink $tableBlock */
        $tableBlock = $blockManager->getBlock("{$this->blockPrefix}.list.table");

        if(isset($params['order']['dir']) && isset($params['order']['column'])){
            $tableBlock->setColumnOrder($params['order']['column'], $params['order']['dir']);
        }

        /** @var Paginator $paginatorBlock */
        $paginatorBlock = $blockManager->getBlock("{$this->blockPrefix}.list.paginator");

        if(isset($params['pageNumber'])){
            $paginatorBlock->setPage($params['pageNumber']);
        }

        $this->addReplacerBlock([$tableBlock, $paginatorBlock]);
    }


    public function updaterevisiontableAction($params, \WP_REST_Request $request)
    {
        $helper = $this->getQuoteHelper();
        $blockManager = $this->getApp()->getBlockManager();

        if(!isset($params['quote_id'])){
            throw new Exception('Quote ID must be provided');
        }

        $quote = $helper->getQuote($params['quote_id']);

        if(!$quote->getId()){
            throw new Exception('The quote could not be found');
        }

        $blockManager->createBlock(
            \SuttonBaker\Impresario\Block\Quote\RevisionsTableContainer::class,
            "{$this->blockPrefix}.past.revisions.table"
        )->setCapabilities($this->getQuoteHelper()->getViewCapabilities())
            ->setParentQuote($quote)
            ->preDispatch();

        $tableBlock = $blockManager->getBlock("{$this->blockPrefix}.revision.list.table");

        if(isset($params['order']['dir']) && isset($params['order']['column'])){
            $tableBlock->setColumnOrder($params['order']['column'], $params['order']['dir']);
        }

        /** @var Paginator $paginatorBlock */
        $paginatorBlock = $blockManager->getBlock("{$this->blockPrefix}.revision.list.paginator");

        if(isset($params['pageNumber'])){
            $paginatorBlock->setPage($params['pageNumber']);
        }

        $this->addReplacerBlock([$tableBlock, $paginatorBlock]);
    }

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function deleteAction($params, \WP_REST_Request $request)
    {
        /** @var \SuttonBaker\Impresario\Helper\Quote $helper */
        $helper = $this->getQuoteHelper();

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['id'])){
            throw new Exception('The item could not be found');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Quote $item */
        $item = $this->createAppObject(
            \SuttonBaker\Impresario\Definition\Quote::DEFINITION_MODEL
        )->load($params['id']);

        if(!$item->getId()){
            throw new Exception('The item could not be found');
        }

        $helper->deleteQuote($item);
        $this->addMessage('The quote has been removed', Messages::SUCCESS);

        return true;
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Quote
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getQuoteHelper()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\Quote::class);
    }

}