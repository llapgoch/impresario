<?php

namespace SuttonBaker\Impresario\Block\Cost\Form;

use DaveBaker\Core\Definitions\Api;
use \SuttonBaker\Impresario\Definition\Cost as CostDefintion;
use DaveBaker\Core\Definitions\Upload as CoreUploadDefinition;
use SuttonBaker\Impresario\Definition\Upload;
use DaveBaker\Core\Definitions\Roles;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Cost\Form
 */
class Edit
extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'cost_id';
    const PREFIX_KEY = 'cost';
    const PREFIX_NAME = 'Cost';

    /** @var \SuttonBaker\Impresario\Model\Db\Cost */
    protected $modelInstance;
    /** @var \DaveBaker\Core\Model\Db\BaseInterface */
    protected $parentItem;

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();

        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $heading = "Create {$prefixName}";
        $this->modelInstance = $this->getApp()->getRegistry()->get('model_instance');
        $this->parentItem =  $this->getApp()->getRegistry()->get('parent_item');

        $editMode = $this->modelInstance->getId() ? true : false;
        $costTypeName = $this->getCostHelper()->determineCostTypeName($this->modelInstance);

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit")->setGroupTemplate('form/group-vertical.phtml');
        $disabledAttrs = $this->modelInstance->getId() ? [] : ['disabled' => 'disabled'];

        $costInvoiceTypes = $this->createArraySelectConnector()->configure(CostDefintion::getCostInvoiceTypes(true))->getElementData();

        if ($supplierCollection = $this->getSupplierHelper()->getSupplierCollection()) {
            $suppliers = $this->createCollectionSelectConnector()
                ->configure(
                    $supplierCollection,
                    'supplier_id',
                    'supplier_name'
                )->getElementData();
        }

        $elements = $builder->build([
            [
                'name' => 'cost_date',
                'labelName' => 'Invoice Date *',
                'class' => 'js-date-picker',
                'rowIdentifier' => 'invoice_date_supplier',
                'type' => 'Input\Text',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
                'value' => $this->getDateHelper()->currentDateShortLocalOutput(),
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(['minDate' => '-5Y', 'maxDate' => "0"])
                ],
            ], [
                'name' => 'cost_invoice_type',
                'labelName' => 'Invoice Type *',
                'formGroup' => true,
                'type' => 'Select',
                'rowIdentifier' => 'invoice_date_supplier',
                'data' => [
                    'select_options' => $costInvoiceTypes,
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'supplier_id',
                'labelName' => 'Supplier *',
                'formGroup' => true,
                'type' => 'Select',
                'rowIdentifier' => 'invoice_date_supplier',
                'data' => [
                    'select_options' => $suppliers,
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'cost_number',
                'labelName' => 'Invoice Number *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'invoice_number_val',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
            ], [
                'name' => 'value',
                'labelName' => 'Invoice Value *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'invoice_number_val',
                'formGroup' => true,
                'class' => 'js-invoice-value',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
            ], [
                'name' => 'sage_number',
                'labelName' => 'Sage Number',
                'type' => 'Input\Text',
                'rowIdentifier' => 'invoice_number_val',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => [
                    'button_name' => $this->getInvoiceHelper()->getActionVerb($this->modelInstance, false) . " Invoice",
                    'capabilities' => $this->getVariationHelper()->getEditCapabilities()
                ],
                'class' => 'btn-block',
                'rowIdentifier' => 'button_bar',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-8'
                ]
            ], [
                'name' => 'delete_button',
                'rowIdentifier' => 'button_bar',
                'type' => '\DaveBaker\Form\Block\Button',
                'formGroup' => true,
                'attributes' => $disabledAttrs,
                'data' => [
                    'button_name' => 'Remove Invoice',
                    'capabilities' => $this->getInvoiceHelper()->getEditCapabilities(),
                    'js_data_items' => [
                        'type' => 'Cost Invoice',
                        'endpoint' => $this->getUrlHelper()->getApiUrl(
                            CostDefintion::API_ENDPOINT_DELETE,
                            ['id' => $this->modelInstance->getId()]
                        ),
                        'returnUrl' => $this->getUrlHelper()->getRefererUrl()
                    ]
                ],
                'class' => 'btn-block btn-danger js-delete-confirm',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'cost_id',
                'type' => 'Input\Hidden',
                'value' => $this->modelInstance->getId()
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ], [
                'name' => 'invoice_data',
                'type' => 'Input\Hidden',
                'value' => json_encode([
                    'amountRemaining' => (float) $totalAmountRemaining
                ]),
                'class' => 'js-invoice-data'
            ]
        ]);

        $this->addRecordMonitorBlock(
            $this->modelInstance,
            $this->getUrlHelper()->getApiUrl(CostDefintion::API_ENDPOINT_RECORD_MONITOR)
        );

        $this->addChildBlock(array_values($elements));

        // Create the file uploader
        $this->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Upload\TableContainer',
                "{$prefixKey}.file.upload.container"
            )->setOrder('before', "cost.edit.button.bar")
                ->setUploadType(
                    $this->modelInstance->getId() ? Upload::TYPE_COST : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY
                )
                ->setIdentifier(
                    $this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession()
                )
        );

        if ($this->getCostHelper()->currentUserCanEdit() == false) {
            $this->lock();
        }
    }

    /**
     * @return \SuttonBaker\Impresario\Block\Form\Base
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preRender()
    {
        $entityId = $this->getRequest()->getParam(self::ID_KEY);
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;
        $uploadTable = $this->getBlockManager()->getBlock('upload.tile.block');
        $uploadParams = [
            'upload_type' => $this->modelInstance->getId() ? Upload::TYPE_COST : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY,
            'identifier' => $this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession()
        ];

        if ($this->getUserHelper()->hasCapability(Roles::CAP_UPLOAD_FILE_ADD)) {
            $uploadTable->addChildBlock(
                $uploadTable->createBlock(
                    '\DaveBaker\Core\Block\Components\FileUploader',
                    "{$prefixKey}.file.uploader",
                    'header_elements'
                )->addJsDataItems(
                    ['endpoint' => $this->getUrlHelper()->getApiUrl(
                        Api::ENDPOINT_FILE_UPLOAD,
                        $uploadParams
                    )]
                )
            );
        }
        return parent::_preRender();
    }
}
