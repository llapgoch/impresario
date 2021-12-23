<?php

namespace SuttonBaker\Impresario\Block\Variation\Form;

use DaveBaker\Core\Definitions\Api;
use SuttonBaker\Impresario\Definition\Upload;
use \SuttonBaker\Impresario\Definition\Variation as VariationDefinition;
use DaveBaker\Core\Definitions\Upload as CoreUploadDefinition;
use SuttonBaker\Impresario\Model\Db\Variation;
use DaveBaker\Core\Definitions\Roles;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Invoice\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'variation_id';
    const PREFIX_KEY = 'variation';
    const PREFIX_NAME = 'Variation';

    /** @var Variation */
    protected $modelInstance;

    /**
     * @return \SuttonBaker\Impresario\Block\Form\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\SelectConnector\Exception
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();

        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;
        $this->addClass('js-loader');

        $heading = "Create {$prefixName}";
        $editMode = false;

        $this->modelInstance = $this->getApp()->getRegistry()->get('model_instance');

        if($this->modelInstance->getId()){
            $editMode = true;
        }

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit")->setGroupTemplate('form/group-vertical.phtml');

        $statuses =  VariationDefinition::getStatuses();
        if($this->statusIsLocked()){
            unset($statuses[VariationDefinition::STATUS_OPEN]);
        }

        // Statuses
        $statuses = $this->createArraySelectConnector()->configure($statuses)->getElementData();
        $disabledAttrs = $this->modelInstance->getId() ? [] : ['disabled' => 'disabled'];


        $elements = $builder->build([
            [
                'name' => 'net_cost',
                'labelName' => 'Net Cost *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'variation_values',
                'attributes' => ['placeholder' => "£"],
                'class' => 'js-net-cost',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
            ], [
                'name' => 'value',
                'labelName' => 'Variation Sell *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'variation_values',
                'attributes' => ['placeholder' => "£"],
                'class' => 'js-net-sell',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
            ], [
                'name' => 'profit',
                'formGroup' => true,
                'rowIdentifier' => 'profit_gp',
                'labelName' => 'Profit',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly'],
                'class' => 'js-profit-calculate',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'gp',
                'formGroup' => true,
                'rowIdentifier' => 'profit_gp',
                'labelName' => 'GP',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly'],
                'class' => 'js-gp-calculate',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'description',
                'labelName' => 'Description *',
                'type' => 'Textarea',
                'formGroup' => true
            ], [
                'name' => 'status',
                'rowIdentifier' => 'status_po_date_approved',
                'labelName' => 'Status *',
                'formGroup' => true,
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
                'data' => [
                    'select_options' => $statuses,
                    'show_first_option' => false,
                    'ignore_lock' => $this->getVariationHelper()->currentUserCanEdit()
                ]
            ], [
                'name' => 'po_number',
                'labelName' => 'PO Number',
                'formGroup' => true,
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off'],
                'rowIdentifier' => 'status_po_date_approved',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'date_approved',
                'labelName' => 'Date Approved',
                'class' => 'js-date-picker',
                'rowIdentifier' => 'status_po_date_approved',
                'type' => 'Input\Text',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(['minDate' => '-5Y', 'maxDate' => "0"])
                ],
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => [
                    'button_name' => $this->getVariationHelper()->getActionVerb($this->modelInstance, false) . ' Variation',
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
                    'button_name' => 'Remove Variation',
                    'capabilities' => $this->getInvoiceHelper()->getEditCapabilities(),
                    'js_data_items' => [
                        'type' => 'Variation',
                        'endpoint' => $this->getUrlHelper()->getApiUrl(
                            VariationDefinition::API_ENDPOINT_DELETE,
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
                'name' => 'variation_id',
                'type' => 'Input\Hidden',
                'value' => $this->modelInstance->getId()
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ]
        ]);

        $this->addRecordMonitorBlock(
            $this->modelInstance,
            $this->getUrlHelper()->getApiUrl(VariationDefinition::API_ENDPOINT_RECORD_MONITOR)
        );

        $this->addChildBlock(array_values($elements));

        if($this->getVariationHelper()->currentUserCanEdit() == false || $this->statusIsLocked()) {
            $this->lock();
        }

        // Create the file uploader
        $this->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Upload\TableContainer',
                "{$prefixKey}.file.upload.container"
            )->setOrder('before', "variation.edit.button.bar")
                ->setUploadType($this->modelInstance->getId() ? Upload::TYPE_VARIATION : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY)
                ->setIdentifier($this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession())
        );
    }

    /**
     * @return bool
     */
    protected function statusIsLocked()
    {
        if(!$this->modelInstance->getId()){
            return false;
        }

        return $this->modelInstance->getStatus() !== VariationDefinition::STATUS_OPEN;
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

        if(!$this->isLocked()) {
            $uploadTable = $this->getBlockManager()->getBlock('upload.tile.block');
            $uploadParams = [
                'upload_type' => $this->modelInstance->getId() ? Upload::TYPE_VARIATION : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY,
                'identifier' => $this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession()
            ];

            if($this->getUserHelper()->hasCapability(Roles::CAP_UPLOAD_FILE_ADD)){
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
        }
        return parent::_preRender();
    }

}