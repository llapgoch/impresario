<?php

namespace SuttonBaker\Impresario\Block\Enquiry\Form;

use DaveBaker\Core\Definitions\Api;
use DaveBaker\Core\Definitions\Table;
use DaveBaker\Form\Validation\Validator;
use \SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;
use SuttonBaker\Impresario\Definition\Enquiry;
use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use SuttonBaker\Impresario\Definition\Upload;
use DaveBaker\Core\Definitions\Upload as CoreUploadDefinition;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit
    extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'enquiry_id';
    const PREFIX_KEY = 'enquiry';
    const PREFIX_NAME = 'Enquiry';

    /** @var \DaveBaker\Core\Model\Db\Core\Upload\Collection */
    protected $attachmentCollection;

    /**
     * @return \DaveBaker\Form\Block\Form|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\SelectConnector\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();

        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        wp_enqueue_script('impresario_form_validator');
        $this->addClass('js-validate-form');

        $this->addJsDataItems(
            ['endpoint' => $this->getUrlHelper()->getApiUrl(Enquiry::API_ENDPOINT_SAVE_VALIDATOR)]
        );

        $entityInstance = $this->getApp()->getRegistry()->get('model_instance');
        $quoteEntity = $this->getQuoteHelper()->getNewestQuoteForEnquiry($entityInstance);

        $this->addClass('js-enquiry-form');
        $this->addAttribute(
            ['data-update-url' => $this->getApp()->getApiManager()->getUrl(
                'client/update',
                ['clientid' => $entityInstance->getId() ? $entityInstance->getId() : 0]
            )]
        );

        // Clients
        $clients = $this->createCollectionSelectConnector()
            ->configure(
                $this->getClientHelper()->getClientCollection(),
                'client_id',
                'client_name'
            )->getElementData();


        // PMs
        $assignedToUsers = [];

        if($csUsers = $this->getRoleHelper()->getCustomerServiceUsers()) {
            $assignedToUsers = $this->createCollectionSelectConnector()
                ->configure(
                    $csUsers,
                    'ID',
                    'display_name'
                )->getElementData();
        }

        // Engineers
        if($engineers = $this->getRoleHelper()->getEngineers()) {
            $engineers = $this->createCollectionSelectConnector()
                ->configure(
                    $engineers,
                    'ID',
                    'display_name'
                )->getElementData();
        }

        // Statuses
        $statuses = $this->createArraySelectConnector()->configure(EnquiryDefinition::getStatuses())->getElementData();
        $ignoreLockValue = false;

        if($this->getEnquiryHelper()->currentUserCanEdit() && !$entityInstance->getIsDeleted()){
            $ignoreLockValue = true;
        }

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName('enquiry_edit')
            ->setGroupTemplate('form/group-vertical.phtml');

        $deleteAttrs = $quoteEntity->getId()
            || $entityInstance->getId() == null
            || $entityInstance->getIsDeleted() ? ['disabled' => 'disabled'] : [];

        $updateAttrs = $entityInstance->getIsDeleted() ? ['disabled' => 'disabled'] : [];

        $returnUrl = $this->getRequest()->getReturnUrl() ?
            $this->getRequest()->getReturnUrl() :
            $this->getUrlHelper()->getPageUrl(Page::ENQUIRY_LIST);

        $elements = $builder->build([
            [
                'name' => 'site_name',
                'labelName' => 'Site Name *',
                'type' => 'Input\Text',
                'formGroup' => true,
            ], [

                'name' => 'date_received',
                'labelName' => 'Date Received *',
                'formGroup' => true,
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off'],
                'value' => $this->getDateHelper()->currentDateShortLocalOutput(),
                'rowIdentifier' => 'date_received_row',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'target_date',
                'labelName' => 'Target Date *',
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'formGroup' => true,
                'rowIdentifier' => 'date_received_row',
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '0', 'maxDate' => "+5Y"]
                    )
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'client_id',
                'labelName' => 'Client *',
                'formGroup' => true,
                'type' => 'Select',
                'rowIdentifier' => 'client_row_one',
                'data' => [
                    'select_options' => $clients
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'client_reference',
                'labelName' => 'Client Reference *',
                'formGroup' => true,
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off'],
                'rowIdentifier' => 'client_row_one',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'po_number',
                'labelName' => 'PO Number',
                'formGroup' => true,
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off'],
                'rowIdentifier' => 'po_mi_mw_numbers',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'mi_number',
                'labelName' => 'MI Number',
                'formGroup' => true,
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off'],
                'rowIdentifier' => 'po_mi_mw_numbers',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'nm_mw_number',
                'labelName' => 'NM/MW Number',
                'formGroup' => true,
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off'],
                'rowIdentifier' => 'po_mi_mw_numbers',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'assigned_to_id',
                'labelName' => 'Assigned To *',
                'type' => 'Select',
                'formGroup' => true,
                'rowIdentifier' => 'assigned_engineer',
                'data' => [
                    'select_options' => $assignedToUsers
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'engineer_id',
                'labelName' => 'Engineer',
                'type' => 'Select',
                'formGroup' => true,
                'rowIdentifier' => 'assigned_engineer',
                'class' => 'js-engineer',
                'data' => [
                    'select_options' => $engineers
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],[
                'name' => 'notes',
                'labelName' => 'Notes',
                'formGroup' => true,
                'type' => 'Textarea'
            ], [
                'name' => 'status',
                'labelName' => 'Enquiry Status *',
                'rowIdentifier' => 'creation_data',
                'formGroup' => true,
                'type' => 'Select',
                'show_first_option' => false,
                'class' => 'js-status',
                'data' => [
                    'select_options' => $statuses,
                    'show_first_option' => false,
                    'ignore_lock' => $ignoreLockValue
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
            ], [
                'name' => 'date_completed',
                'labelName' => 'Date Completed',
                'formGroup' => true,
                'class' => [
                    'js-date-picker',
                    'js-date-completed'
                ],
                'data' => ['ignore_lock' => $ignoreLockValue],
                'rowIdentifier' => 'creation_data',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'type' => 'Input\Text',
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '', 'maxDate' => "0"]
                    )]
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'formGroup' => true,
                'rowIdentifier' => 'button_bar',
                'attributes' => $updateAttrs,
                'data' => [
                    'button_name' => $this->getEnquiryHelper()->getActionVerb($entityInstance, false) . " Enquiry",
                    'capabilities' => $this->getEnquiryHelper()->getEditCapabilities()
                ],
                'class' => 'btn-block',
                'formGroupSettings' => [
                    'class' => 'col-md-8'
                ]
            ], [
                'name' => 'delete_button',
                'rowIdentifier' => 'button_bar',
                'type' => '\DaveBaker\Form\Block\Button',
                'formGroup' => true,
                'attributes' => $deleteAttrs,
                'data' => [
                    'button_name' => 'Remove Enquiry',
                    'capabilities' => $this->getEnquiryHelper()->getEditCapabilities(),
                    'js_data_items' => [
                        'type' => 'Enquiry',
                        'endpoint' => $this->getUrlHelper()->getApiUrl(
                            EnquiryDefinition::API_ENDPOINT_DELETE,
                            ['id' => $entityInstance->getId()]
                        ),
                        'returnUrl' => $returnUrl
                    ]
                ],
                'class' => 'btn-block btn-danger js-delete-confirm',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'enquiry_id',
                'type' => 'Input\Hidden',
                'value' => $entityInstance->getId()
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit',
                'data' => ['ignore_lock' => $ignoreLockValue]
            ], [
                'name' => 'enquiry_data',
                'type' => 'Input\Hidden',
                'value' => json_encode([
                    'hasQuote' => ($quoteEntity->getId() ? 1 : 0),
                    'completedStatus' => EnquiryDefinition::STATUS_COMPLETE
                ]),
                'class' => 'js-enquiry-data'
            ]
        ]);


        if($entityInstance->getId()) {
            $this->taskTableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\TableContainer',
                "{$prefixKey}.task.table"
            )->setOrder('after', 'enquiry.edit.notes.form.group')
                ->setCapabilities($this->getTaskHelper()->getViewCapabilities());

            $this->taskTableBlock->setInstanceCollection(
                $collection = $this->getTaskHelper()->getTaskCollectionForEntity(
                    $entityInstance->getId(),
                    TaskDefinition::TASK_TYPE_ENQUIRY
                )
            )->setEditLinkParams([
                \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
            ]);


            $this->addChildBlock($this->taskTableBlock);
        }

        $enquiryIsClosed = in_array($entityInstance->getStatus(),
            [EnquiryDefinition::STATUS_COMPLETE, EnquiryDefinition::STATUS_CANCELLED]);

        if($enquiryIsClosed || $entityInstance->getIsDeleted()){
            $this->addChildBlock(
                $this->createBlock(
                    '\SuttonBaker\Impresario\Block\Form\LargeMessage',
                    "{$prefixKey}.warning.message"
                )->setMessage("This {$prefixName} " . ($entityInstance->getIsDeleted() ? "has been removed" : "is locked"))
                ->setMessageType($entityInstance->getIsDeleted() ? 'danger' : 'warning')
            );
        }

        $this->addChildBlock(array_values($elements));

        // Create the file uploader

        $this->addChildBlock(
            $this->createBlock(
            '\SuttonBaker\Impresario\Block\Upload\TableContainer',
            "{$prefixKey}.file.upload.container"
            )->setOrder('before', "enquiry.edit.button.bar")
            ->setUploadType($entityInstance->getId() ? Upload::TYPE_ENQUIRY : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY)
            ->setIdentifier($entityInstance->getId() ? $entityInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession())
        );

        if($enquiryIsClosed || $this->getEnquiryHelper()->currentUserCanEdit() == false){
            $this->lock();
        }
    }

    /**
     * @return \SuttonBaker\Impresario\Block\Form\Base
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preRender()
    {
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $entityInstance = $this->getApp()->getRegistry()->get('model_instance');
        $uploadTable = $this->getBlockManager()->getBlock('upload.tile.block');

        $uploadParams = [
            'upload_type' => $entityInstance->getId() ? Upload::TYPE_ENQUIRY : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY,
            'identifier' => $entityInstance->getId() ? $entityInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession()
        ];

        if(!$this->isLocked()) {
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

        if($entityInstance->getId()) {
            if($tableBlock = $this->getBlockManager()->getBlock('task.table.list.table')) {
                $tableBlock->removeHeader(['task_id', 'task_type'])
                ->addJsDataItems([
                    Table::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                    $this->getUrlHelper()->getApiUrl(
                        TaskDefinition::API_ENDPOINT_UPDATE_TABLE,
                        [
                            'type' => TaskDefinition::TASK_TYPE_ENQUIRY,
                            'parent_id' => $entityInstance->getId()
                        ]
                    )
                ]);
            }

            $paginator = $this->getBlockManager()->getBlock('task.table.list.paginator')
                ->setRecordsPerPage(TaskDefinition::RECORDS_PER_PAGE_INLINE)
                ->removeClass('pagination-xl')->addClass('pagination-xs');


            if($this->isLocked() == false) {
                $addButton = $this->createBlock(
                    '\DaveBaker\Core\Block\Html\Tag',
                    'create.task.button',
                    'header_elements'
                )->setTagText('Create Task')
                    ->setTag('a')
                    ->addAttribute(['href' => $this->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::TASK_EDIT,
                        [
                            'task_type' => \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_ENQUIRY,
                            'parent_id' => $entityInstance->getId()
                        ],
                        true
                    )])
                    ->addClass('btn btn-sm btn-primary')
                    ->setCapabilities($this->getTaskHelper()->getEditCapabilities());


                $this->getBlockManager()->getBlock('task.table.tile.block')
                    ->addChildBlock($addButton);
            }
        }

        return parent::_preRender();
    }


    /**
     * @return \DaveBaker\Form\SelectConnector\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createCollectionSelectConnector()
    {
        return $this->createAppObject('\DaveBaker\Form\SelectConnector\Collection');
    }

    /**
     * @return \DaveBaker\Form\SelectConnector\AssociativeArray
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createArraySelectConnector()
    {
        return $this->createAppObject('\DaveBaker\Form\SelectConnector\AssociativeArray');
    }
}