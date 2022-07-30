<?php

namespace SuttonBaker\Impresario\Block\Cost\Form;

use DaveBaker\Core\Definitions\Api;
use \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
use \SuttonBaker\Impresario\Definition\Invoice as InvoiceDefinition;
use DaveBaker\Core\Definitions\Upload as CoreUploadDefinition;
use SuttonBaker\Impresario\Definition\Upload;
use DaveBaker\Core\Definitions\Roles;
use SuttonBaker\Impresario\Block\Cost\Item\TableContainer;
use SuttonBaker\Impresario\Model\Db\Cost\Item;
use SuttonBaker\Impresario\Model\Db\Cost\Item\Collection;

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

    /** @var string */
    protected $blockPrefix = 'cost';
    /** @var \SuttonBaker\Impresario\Model\Db\Cost */
    protected $modelInstance;
    /** @var \DaveBaker\Core\Model\Db\BaseInterface */
    protected $parentItem;
    /** @var TableContainer */
    protected $costItemBlock;
    /** @var  \SuttonBaker\Impresario\Block\Invoice\TableContainer */
    protected $invoiceTableBlock;

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

        wp_register_script('impresario_cost_edit', get_template_directory_uri() . '/assets/js/cost/edit-controller.js', ['jquery']);
        wp_enqueue_script('impresario_cost_edit');

        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        wp_enqueue_script('impresario_form_validator');

        $this->modelInstance = $this->getApp()->getRegistry()->get('model_instance');
        $this->parentItem =  $this->getApp()->getRegistry()->get('parent_item');


        $editMode = $this->modelInstance->getId() ? true : false;
        $statuses = $this->createArraySelectConnector()->configure(CostDefinition::getStatuses())->getElementData();

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit")->setGroupTemplate('form/group-vertical.phtml');
        $disabledAttrs = $this->modelInstance->getId() ? [] : ['disabled' => 'disabled'];

        $costInvoiceTypes = $this->createArraySelectConnector()->configure(CostDefinition::getCostInvoiceTypes(true))->getElementData();

        $this->addClass('js-validate-form js-form-overlay js-cost-form');
        $this->addJsDataItems([
            'endpointValidateSave' => $this->getUrlHelper()->getApiUrl(CostDefinition::API_ENDPOINT_VALIDATE_SAVE),
            'idElementSelector' => '[name="cost_id"]',
            'idKey' => 'cost_id'
        ]);

        if ($supplierCollection = $this->getSupplierHelper()->getSupplierCollection()) {
            $suppliers = $this->createCollectionSelectConnector()
                ->configure(
                    $supplierCollection,
                    'supplier_id',
                    'supplier_name'
                )->getElementData();
        }

        if ($editMode) {
            $parentId = $this->modelInstance->getParentId();
            $costType = $this->modelInstance->getCostType();
        } else {
            $costType = $this->getRequest()->getParam(CostDefinition::COST_TYPE_PARAM);
            $parentId = $this->getRequest()->getParam(CostDefinition::PARENT_ID_PARAM);
        }

        $amountInvoiced = (float) $this->modelInstance->getAmountInvoiced();

        $this->addChildBlock(
            $this->createFormErrorBlock()
                ->setOrder('before', '')
        );

        $returnUrl = $this->getRequest()->getReturnUrl();

        $elements = $builder->build([
            [
                'name' => 'cost_date',
                'labelName' => 'Purchase Order Date *',
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
                'labelName' => 'Type *',
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
                'name' => 'supplier_quote_number',
                'labelName' => 'Supplier Quote Number',
                'type' => 'Input\Text',
                'rowIdentifier' => 'invoice_number_val',
                'formGroup' => true,
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
                'name' => 'delivery_date',
                'labelName' => 'Delivery Date',
                'class' => 'js-date-picker',
                'rowIdentifier' => 'invoice_number_val',
                'type' => 'Input\Text',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(['minDate' => '-5Y', 'maxDate' => "+5Y"])
                ],
            ], [
                'name' => 'po_item_total',
                'labelName' => 'PO Total',
                'type' => 'Input\Text',
                'rowIdentifier' => 'total_items',
                'formGroup' => true,
                'class' => 'js-po-total-value',
                'attributes' => ['disabled' => 'disabled'],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
            ],
            [
                'name' => 'amount_invoiced_total', // Use a different name to the DB column as we want to set the value and not be overridden
                'labelName' => 'Amount Invoiced',
                'type' => 'Input\Text',
                'rowIdentifier' => 'total_items',
                'formGroup' => true,
                'class' => 'js-amount-invoiced-value',
                'value' => $this->getLocaleHelper()->formatCurrency($amountInvoiced),
                'attributes' => [
                    'disabled' => 'disabled',
                    'data-actual-value' => $amountInvoiced
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
            ], [
                'name' => 'invoice_amount_remaining',
                'labelName' => 'Amount Remaining',
                'type' => 'Input\Text',
                'rowIdentifier' => 'total_items',
                'formGroup' => true,
                'value' => 0,
                'class' => 'js-amount-remaining-value',
                'attributes' => ['disabled' => 'disabled'],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
            ],  [
                'name' => 'status',
                'labelName' => 'Status *',
                'formGroup' => true,
                'type' => 'Select',
                'show_first_option' => false,
                'class' => 'js-status',
                'data' => [
                    'select_options' => $statuses,
                    'show_first_option' => false,
                ],
                // 'formGroupSettings' => [
                //     'class' => 'col-md-4'
                // ],
            ], [
                'name' => 'special_instructions',
                'formGroup' => true,
                'labelName' => 'Special Instructions',
                'type' => 'Textarea',
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => [
                    'button_name' => $this->getInvoiceHelper()->getActionVerb($this->modelInstance, false) . " Purchase Order",
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
                    'button_name' => 'Remove',
                    'capabilities' => $this->getInvoiceHelper()->getEditCapabilities(),
                    'js_data_items' => [
                        'type' => 'Purchase Order',
                        'endpoint' => $this->getUrlHelper()->getApiUrl(
                            CostDefinition::API_ENDPOINT_DELETE,
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
                'name' => 'parent_id',
                'type' => 'Input\Hidden',
                'value' => $parentId
            ], [
                'name' => 'cost_type',
                'type' => 'Input\Hidden',
                'value' => $costType
            ], [
                'name' => 'return_url',
                'type' => 'Input\Hidden',
                'value' => $returnUrl
            ],
        ]);

        $this->addRecordMonitorBlock(
            $this->modelInstance,
            $this->getUrlHelper()->getApiUrl(CostDefinition::API_ENDPOINT_RECORD_MONITOR)
        );

        $this->addChildBlock(array_values($elements));

        $this->createCostItemTableBlock();
        $this->createInvoiceTableBlock();

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
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function createInvoiceTableBlock()
    {
        $prefixKey = $this->blockPrefix;

        // Only allow invoice creation for saved costs
        if (!$this->getInvoiceHelper()->currentUserCanView() || !($this->modelInstance->getId())) {
            return;
        }

        $this->invoiceTableBlock = $this->createBlock(
            \SuttonBaker\Impresario\Block\Invoice\TableContainer::class,
            "{$prefixKey}.invoice.table"
        )->setOrder('after', "{$prefixKey}.edit.total.items")
            ->setHeading('Invoices');

        $this->invoiceTableBlock->setInstanceCollection(
            $this->getInvoiceHelper()->getInvoiceCollectionForEntity(
                $this->modelInstance->getId(),
                InvoiceDefinition::INVOICE_TYPE_PO_INVOICE
            )
        )->setEditLinkParams([
            \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
        ]);


        $this->addChildBlock($this->invoiceTableBlock);
    }

    protected function createCostItemTableBlock()
    {
        $prefixKey = $this->blockPrefix;
        $this->costItemBlock = $this->createBlock(
            \SuttonBaker\Impresario\Block\Cost\Item\TableContainer::class,
            "{$this->blockPrefix}.item.table"
        )->setOrder('before', "{$prefixKey}.edit.total.items");

        $hasItems = false;

        if ($this->modelInstance->getId()) {
            $items = $this->getCostHelper()->getCostInvoiceItems(
                $this->modelInstance->getId()
            )->getItems();


            $this->costItemBlock->setInstanceCollection(
                $this->getCostHelper()->getCostInvoiceItems(
                    $this->modelInstance->getId()
                )
            );

            $hasItems = count($items) > 0;
        }

        if (!$hasItems) {
            // Create a new collection with a blank item
            /** @var Collection $collection */
            $collection = $this->createAppObject(Collection::class);
            /** @var Item $instance */
            $instance = $this->createAppObject(Item::class);

            // Setting the items on a collection will prevent it from loading. Note: this requires changes in feature/p-sytem in core.
            $collection->setItems([$instance]);
            $this->costItemBlock->setInstanceCollection($collection);
        }

        $this->addChildBlock($this->costItemBlock);
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
        $uploadIdentifier = $this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession();

        $uploadParams = [
            'upload_type' => $this->modelInstance->getId() ? Upload::TYPE_COST : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY,
            'identifier' => $uploadIdentifier
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
                    ->setActualType(Upload::TYPE_COST)
                    ->setIdentifier($uploadIdentifier)
            );
        }

        if ($invoiceTileBlock = $this->getBlockManager()->getBlock('invoice.tile.block')) {
            $invoiceTileBlock->addChildBlock(
                $this->createSmallButtonElement(
                    'Create Invoice',
                    $this->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::INVOICE_EDIT,
                        [
                            'invoice_type' => InvoiceDefinition::INVOICE_TYPE_PO_INVOICE,
                            'parent_id' => $this->modelInstance->getId()
                        ],
                        true
                    ),
                    'create.invoice.button',
                    'header_elements'
                )->setCapabilities($this->getInvoiceHelper()->getEditCapabilities())
                    ->addClass('js-invoice-create-button')
            );
        }


        return parent::_preRender();
    }
}
