<?php

namespace SuttonBaker\Impresario\Api;

use DaveBaker\Form\Block\Error\Main;
use \DaveBaker\Core\Definitions\Messages;
use Exception;
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

        /** @var CostConverter $converter */
        $converter = $this->createAppObject(CostConverter::class);
        $formValues = $converter->convert($params['formValues']);

        $modelInstance = $helper->getCost();
        $navigatingAway = isset($params['navigatingAway']) && $params['navigatingAway'] ? true : false;


        if (isset($formValues['cost_id']) && $formValues['cost_id']) {
            $modelInstance->load($formValues['cost_id']);

            if (!$modelInstance->getId()) {
                throw new \Exception('The cost could not be found');
            }
        }

        $isEditMode = $modelInstance->getId() ? true : false;

        // if we're creating new, we have to have a return url for the project - always redirect on create as the form would need to reload to do anything
        if (!$formValues['return_url'] && !$isEditMode) {
            throw new \Exception("Return url not provided");
        }

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

        return array_merge($validateResult, $saveValues);
    }

    protected function saveCost(
        \SuttonBaker\Impresario\Model\Db\Cost $modelInstance,
        $formValues,
        $navigatingAway = false
    ) {
        $saveValues = $this->getCostHelper()->saveCost($modelInstance, $formValues);

        $message = "The purchase order has been " . ($saveValues['new_save'] ? 'created' : 'updated');

        // Always redirect back to the project on creation - allow staing on the page in 
        if ($saveValues['new_save']) {
            // Redirect to the return url if we're saving new, or let the user navigate away
            if (!$navigatingAway) {
                // This has already been validated
                $saveValues['redirect'] = $formValues['return_url'];
            }

            $this->getApp()->getGeneralSession()->addMessage(
                $message,
                Messages::SUCCESS
            );
        } else {
            // If we're editing, allow the user to stay on the page, unless navigating away
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
        }

        // Only render the block if we're staying on the page (edit mode only)
        if ($saveValues['new_save'] == false) {
            // Add the PO Item block to populate with IDs
            /** @var \SuttonBaker\Impresario\Block\Cost\Item\TableContainer $costItemBlock */
            $costItemBlock = $this->getApp()->getBlockManager()->createBlock(
                \SuttonBaker\Impresario\Block\Cost\Item\TableContainer::class,
                "{$this->blockPrefix}.item.table"
            );

            $costItemBlock->setInstanceCollection(
                $this->getCostHelper()->getCostInvoiceItems(
                    $modelInstance->getId()
                )
            );

            $costItemBlock->preDispatch();

            if ($tableBlock = $this->getApp()->getBlockManager()->getBlock('cost.item.list.table')) {
                $this->addReplacerBlock($tableBlock);
            }
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
            throw new \Exception('The purchase order could not be found');
        }

        $helper->deleteCost($item);
        $this->addMessage('The purchase order has been removed', Messages::SUCCESS);

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
