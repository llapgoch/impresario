<?php

namespace SuttonBaker\Impresario\Block\Variation\Form;

use DaveBaker\Core\Definitions\Api;
use SuttonBaker\Impresario\Definition\Upload;
use \SuttonBaker\Impresario\Definition\Variation as VariationDefinition;
use DaveBaker\Core\Definitions\Upload as CoreUploadDefinition;
use SuttonBaker\Impresario\Model\Db\Variation;

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
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     */
    protected function _preDispatch()
    {
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $heading = "Create {$prefixName}";
        $editMode = false;

        $this->modelInstance = $this->getApp()->getRegistry()->get('model_instance');

        if($this->modelInstance->getId()){
            $editMode = true;
        }

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit")->setGroupTemplate('form/group-vertical.phtml');


        $elements = $builder->build([
            [
                'name' => 'date_approved',
                'labelName' => 'Date Approved *',
                'class' => 'js-date-picker',
                'rowIdentifier' => 'date_approved',
                'type' => 'Input\Text',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'value' => $this->getDateHelper()->currentDateShortLocalOutput(),
                'attributes' => [
                    'readonly' => 'readonly',
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(['minDate' => '-5Y', 'maxDate' => "0"])
                ],
            ], [
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
                'labelName' => 'Variation Value *',
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
                'attributes' => ['disabled' => 'disabled'],
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
                'attributes' => ['disabled' => 'disabled'],
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
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => [
                    'button_name' => $editMode ? 'Update Variation' : 'Create Variation',
                    'capabilities' => $this->getVariationHelper()->getEditCapabilities()
                ],
                'class' => 'btn-block'
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

        $this->addChildBlock(array_values($elements));

        if($this->getVariationHelper()->currentUserCanEdit() == false) {
            $this->lock();
        }

        // Create the file uploader
        $this->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Upload\TableContainer',
                "{$prefixKey}.file.upload.container"
            )->setOrder('before', "{$prefixKey}.edit.submit.element")
                ->setUploadType($this->modelInstance->getId() ? Upload::TYPE_VARIATION : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY)
                ->setIdentifier($this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession())
        );
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