<?php

namespace SuttonBaker\Impresario\Api;
use DaveBaker\Core\Api\Exception;
use DaveBaker\Core\Block\Components\Paginator;
use DaveBaker\Core\Definitions\Messages;
use DaveBaker\Form\Validation\Validator;
use SuttonBaker\Impresario\Block\Table\StatusLink;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Form\EnquiryConfigurator;

/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Api
 *
 */
class Enquiry
    extends \DaveBaker\Core\Api\Base
{
    /** @var string  */
    protected $blockPrefix = 'enquiry';
    /** @var array  */
    protected $capabilities = [Roles::CAP_VIEW_ENQUIRY];

    public function savevalidatorAction($params, \WP_REST_Request $request)
    {
        if(!isset($params['formValues'])){
            throw new Exception('No form values provided');
        }
        /** @var EnquiryConfigurator $configurator */
        $configurator = $this->createAppObject(EnquiryConfigurator::class);

        /** @var Validator $validator */
        $validator = $this->createAppObject(Validator::class)
            ->setValues($params['formValues']);
        $validator->configurate($configurator)->validate();

        return [
            'hasErrors' => $validator->hasErrors(),
            'errorFields' => $validator->getErrorFields()
        ];
    }
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
        /** @var \SuttonBaker\Impresario\Helper\Enquiry $helper */
        $helper = $this->createAppObject('\SuttonBaker\Impresario\Helper\Enquiry');

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['id'])){
            throw new Exception('The item could not be found');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Enquiry $item */
        $item = $this->createAppObject(
            \SuttonBaker\Impresario\Definition\Enquiry::DEFINITION_MODEL
        )->load($params['id']);

        if(!$item->getId()){
            throw new Exception('The item could not be found');
        }

        $helper->deleteEnquiry($item);
        $this->addMessage('The enquiry has been removed', Messages::SUCCESS);

        return true;
    }

}