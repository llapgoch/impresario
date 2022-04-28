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

    protected $nonUserValues = [
        'cost_id',
        'created_by_id',
        'last_edited_by_id',
        'cost_type',
        'parent_id',
        'created_at',
        'updated_at',
        'is_deleted'
    ];

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
        $converter = $this->createAppObject(CostConverter::class);
        $navigatingAway = isset($params['navigatingAway']) && $params['navigatingAway'] ? true : false;
        $formValues = $params['formValues'];


        if (isset($formValues['cost_id']) && $formValues['cost_id']) {
            $modelInstance->load($formValues['cost_id']);

            if (!$modelInstance->getId()) {
                throw new \Exception('The cost could not be found');
            }
        }

        $validateResult = $this->validateValues($modelInstance, $formValues);

        if($validateResult['hasErrors']){
            return $validateResult;
        }
        
        exit;




        if (!$validator->validate()) {
            return $this->prepareFormErrors($validator);
        }

        $this->saveFormValues($postParams);

        if (!$this->getApp()->getResponse()->redirectToReturnUrl()) {
            $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::PROJECT_LIST);
        }
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
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function saveFormValues($data)
    {
        if (!$this->getApp()->getHelper('User')->isLoggedIn()) {
            return;
        }

        foreach ($this->nonUserValues as $nonUserValue) {
            if (isset($data[$nonUserValue])) {
                unset($data[$nonUserValue]);
            }
        }

        $newSave = false;

        // Add created by user
        if (!$this->modelInstance->getId()) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
            $data['cost_type'] = $this->costType;
            $data['parent_id'] = $this->parentItem->getId();
            $newSave = true;
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        $this->modelInstance->setData($data)->save();

        if ($newSave && ($temporaryId = $this->getRequest()->getPostParam(Upload::TEMPORARY_IDENTIFIER_ELEMENT_NAME))) {
            // Assign any uploads to the enquiry
            $this->getUploadHelper()->assignTemporaryUploadsToParent(
                $temporaryId,
                \SuttonBaker\Impresario\Definition\Upload::TYPE_COST,
                $this->modelInstance->getId()
            );
        }

        $this->addMessage(
            "The cost has been " . ($newSave ? 'created' : 'updated'),
            Messages::SUCCESS
        );

        return $this;
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
