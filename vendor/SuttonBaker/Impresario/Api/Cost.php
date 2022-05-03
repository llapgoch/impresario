<?php

namespace SuttonBaker\Impresario\Api;

use DaveBaker\Form\Block\Error\Main;
use \DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Definition\Cost as CostDefinition;
use SuttonBaker\Impresario\SaveConverter\Cost as CostConverter;

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



    public function validatesaveAction(
        $params,
        \WP_REST_Request $request
    ) {
        // Form submission
        $helper = $this->getCostHelper();

        if (!isset($params['formValues'])) {
            throw new \Exception('No form values provided');
        }

        $modelInstance = $helper->getCost();
        $navigatingAway = isset($params['navigatingAway']) && $params['navigatingAway'] ? true : false;
        /** @var CostConverter $converter */
        $converter = $this->createAppObject(CostConverter::class);
        $formValues = $converter->convert($params['formValues']);

        if (isset($formValues['cost_id']) && $formValues['cost_id']) {
            $modelInstance->load($formValues['cost_id']);

            if (!$modelInstance->getId()) {
                throw new \Exception('The cost could not be found');
            }
        }

        $isEditMode = $modelInstance->getId() ? true : false;
        $validateResult = $this->validateValues($modelInstance, $formValues);

        if ($validateResult['hasErrors']) {
            return $validateResult;
        }

        // Save the cost item here & add messages
        $saveValues = $this->saveCost(
            $modelInstance,
            $formValues,
            $navigatingAway
        );

        // Add the PO Item block to populate with IDs
        /** @var \SuttonBaker\Impresario\Block\Cost\Item\TableContainer $costItemBlock */
        $costItemBlock = $this->getApp()->getBlockManager()->createBlock(
            \SuttonBaker\Impresario\Block\Cost\Item\TableContainer::class,
            "{$this->blockPrefix}.item.table"
        );

        if ($isEditMode) {
            $costItemBlock->setInstanceCollection(
                $this->getCostHelper()->getCostInvoiceItems(
                    $modelInstance->getId()
                )
            );
        }

        $costItemBlock->preDispatch();
        
        if($tableBlock = $this->getApp()->getBlockManager()->getBlock('cost.item.list.table')) {
            $this->addReplacerBlock($tableBlock);
        }
        

        return array_merge($validateResult, $saveValues);
    }

    protected function saveCost(
        \SuttonBaker\Impresario\Model\Db\Cost $modelInstance,
        $formValues,
        $navigatingAway = false
    ) {
        $saveValues = $this->getCostHelper()->saveCost($modelInstance, $formValues);
        $message = "The cost has been " . ($saveValues['new_save'] ? 'created' : 'updated');

        if ($navigatingAway) {
            $this->getApp()->getGeneralSession()->addMessage(
                $message,
                Messages::SUCCESS
            );
        } else {
            $this->addReplacerBlock(
                $this->getModalHelper()->createAutoOpenModal(
                    'Success',
                    $message
                )
            );
        }

        return $saveValues;
    }

    protected function validateValues(
        \SuttonBaker\Impresario\Model\Db\Cost $modelInstance,
        $formValues
    ) {
        $blockManager = $this->getApp()->getBlockManager();
        /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
        $configurator = $this->createAppObject(\SuttonBaker\Impresario\Form\CostConfigurator::class)->setModel($modelInstance);

        /** @var \DaveBaker\Form\Validation\Validator $validator */
        $validator = $this->createAppObject(\DaveBaker\Form\Validation\Validator::class)
            ->setValues($formValues);

        $validator->configurate($configurator)->validate();

        /** @var Main $errorBlock */
        $errorBlock = $blockManager->createBlock(Main::class, 'cost.edit.form.errors');
        $errorBlock->addErrors($validator->getErrors())->setIsReplacerBlock(true);

        $this->addReplacerBlock($errorBlock);

        return [
            'hasErrors' => $validator->hasErrors(),
            'errorFields' => $validator->getErrorFields()
        ];
    }


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

        if (!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if (!isset($params['id'])) {
            throw new \Exception('The item could not be found');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Cost $item */
        $item = $this->createAppObject(
            CostDefinition::DEFINITION_MODEL
        )->load($params['id']);

        if (!$item->getId()) {
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
        if (!isset($params['id'])) {
            throw new \Exception('ID is required');
        }

        $object = $this->getCostHelper()->getCost($params['id']);
        return $this->performRecordMonitor($params, $object);
    }
}
