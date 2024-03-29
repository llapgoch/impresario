<?php

namespace SuttonBaker\Impresario\Block\Quote\Form;

use DaveBaker\Core\Definitions\Api;
use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use SuttonBaker\Impresario\Definition\Upload;
use DaveBaker\Core\Definitions\Upload as CoreUploadDefinition;
use SuttonBaker\Impresario\Definition\Priority as PriorityDefinition;
use DaveBaker\Core\Definitions\Roles;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'quote_id';
    protected $prefixName = 'Quote';
    protected $blockPrefix = 'quote';

    /** @var \SuttonBaker\Impresario\Block\Task\TableContainer */
    protected $taskTableBlock;
    /** @var \SuttonBaker\Impresario\Block\Quote\RevisionsTableContainer */
    protected $revisionsBlock;
    /** @var \SuttonBaker\Impresario\Model\Db\Quote */
    protected $modelInstance;

    /**
     * @return \DaveBaker\Form\Block\Form|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\SelectConnector\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();

        wp_enqueue_script('impresario_form_validator');
        $this->addClass('js-validate-form js-action-form js-quote-form js-form-overlay');

        wp_register_script('impresario_action_switcher', get_template_directory_uri() . '/assets/js/action.switcher.widget.js');
        wp_enqueue_script('impresario_action_switcher');

        $this->modelInstance = $this->getApp()->getRegistry()->get('model_instance');

        $this->addJsDataItems(
            [
                'endpointValidateSave' => $this->getUrlHelper()->getApiUrl(QuoteDefinition::API_ENDPOINT_VALIDATE_SAVE),
                'endpointSave' => $this->getUrlHelper()->getApiUrl(QuoteDefinition::API_ENDPOINT_SAVE),
                'idElementSelector' => '[name="quote_id"]',
                'idKey' => 'quote_id'
            ]
        );

        $projectEntity = $this->getProjectHelper()->getProjectForQuote($this->modelInstance->getId());
        $groupQuoteEntity = $this->getQuoteHelper()->getQuoteForEnquiry($this->modelInstance->getEnquiryId());
        $groupProjectEntity = $this->getProjectHelper()->getProjectForQuote($groupQuoteEntity->getId());
        // This logic should show whether the current quote has made the quote group's project
        $finalQuoteNotCurrent = ($groupQuoteEntity->getId() !== $this->modelInstance->getId()) && $groupProjectEntity->getId();

        $quoteProjectTypeCollection = $this->getQuoteHelper()->getQuoteProjectTypeCollection();
        $quoteProjectTypes = $this->createCollectionSelectConnector()
            ->configure(
                $quoteProjectTypeCollection,
                'type_id',
                'name'
            )->getElementData();


        // Estimators
        if ($estimators = $this->getRoleHelper()->getEstimators()) {
            $estimators = $this->createCollectionSelectConnector()
                ->configure(
                    $estimators,
                    'ID',
                    'display_name'
                )->getElementData();
        }

        // Completed Users
        $completedUsers = $this->createCollectionSelectConnector()
            ->configure(
                $this->getApp()->getHelper('User')->getUserCollection(),
                'ID',
                'display_name'
            )->getElementData();

        // Clients
        $clients = $this->createCollectionSelectConnector()
            ->configure(
                $this->getClientHelper()->getClientCollection(),
                'client_id',
                'client_name'
            )->getElementData();

        // Statuses
        $statuses = $this->createArraySelectConnector()->configure(
            QuoteDefinition::getStatuses()
        )->getElementData();

        $priorities = $this->createArraySelectConnector()->configure(PriorityDefinition::getStatuses())->getElementData();

        $tenderStatuses = $this->createArraySelectConnector()->configure(
            QuoteDefinition::getTenderStatuses()
        )->getElementData();

        $ignoreLockValue = false;

        if (($this->getQuoteHelper()->currentUserCanEdit()
                && !$this->modelInstance->getIsDeleted()) && $finalQuoteNotCurrent == false
        ) {
            $ignoreLockValue = true;
        }

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$this->blockPrefix}_edit")->setGroupTemplate('form/group-vertical.phtml');

        $deleteAttrs = $projectEntity->getId()
            || $this->modelInstance->getId() == null
            || $this->modelInstance->getIsDeleted() ? ['disabled' => 'disabled'] : [];

        $updateAttrs = $this->modelInstance->getIsDeleted()
            || $finalQuoteNotCurrent ? ['disabled' => 'disabled'] : [];

        $reviseAttrs = $projectEntity->getId()
            || $this->modelInstance->getId() == null
            || $this->modelInstance->getIsDeleted()
            || $this->modelInstance->getTenderStatus() == QuoteDefinition::TENDER_STATUS_WON
            || $finalQuoteNotCurrent
            ? ['disabled' => 'disabled'] : [];

        $returnUrl = $this->getRequest()->getReturnUrl() ?
            $this->getRequest()->getReturnUrl() :
            $this->getUrlHelper()->getPageUrl(Page::QUOTE_LIST);

        $this->addRecordMonitorBlock(
            $this->modelInstance,
            $this->getUrlHelper()->getApiUrl(QuoteDefinition::API_ENDPOINT_RECORD_MONITOR)
        );

        $this->addChildBlock(
            $this->createFormErrorBlock()
                ->setOrder('before', '')
        );

        $elements = $builder->build([
            [
                'name' => 'site_name',
                'labelName' => 'Site Name *',
                'formGroup' => true,
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly'],
                'rowIdentifier' => 'site_name_type',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'type_id',
                'formGroup' => true,
                'rowIdentifier' => 'site_name_type',
                'labelName' => 'Category Type',
                'data' => [
                    'select_options' => $quoteProjectTypes
                ],
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'project_name',
                'formGroup' => true,
                'rowIdentifier' => 'project_name_priority',
                'labelName' => 'Project Name *',
                'type' => 'Input\Text',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],
            [
                'name' => 'priority',
                'labelName' => 'Priority *',
                'rowIdentifier' => 'project_name_priority',
                'formGroup' => true,
                'type' => 'Select',
                'data' => [
                    'select_options' => $priorities,
                    'show_first_option' => false,
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'date_received',
                'labelName' => 'Date Received *',
                'type' => 'Text',
                'formGroup' => true,
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off'],
                'rowIdentifier' => 'date_received_row',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'client_id',
                'formGroup' => true,
                'rowIdentifier' => 'client_reference_row',
                'labelName' => 'Client',
                'data' => [
                    'select_options' => $clients,
                    'locked' => true
                ],
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
                'attributes' => [
                    'readonly' => 'readonly'
                ]
            ], [
                'name' => 'client_reference',
                'formGroup' => true,
                'labelName' => 'Client Reference',
                'type' => 'Input\Text',
                'rowIdentifier' => 'client_reference_row',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
                'attributes' => [
                    'readonly' => 'readonly'
                ]
            ], [
                'name' => 'client_requested_by',
                'formGroup' => true,
                'labelName' => 'Client Requested By',
                'type' => 'Input\Text',
                'rowIdentifier' => 'client_reference_row',
                'attributes' => [
                    'readonly' => 'readonly'
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
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
                'name' => 'date_required',
                'formGroup' => true,
                'labelName' => 'Required By Date *',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'rowIdentifier' => 'date_received_row',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['maxDate' => "+5Y"]
                    )
                ]
            ], [
                'name' => 'estimator_id',
                'rowIdentifier' => 'cost_values',
                'formGroup' => true,
                'labelName' => 'Estimator',
                'data' => [
                    'select_options' => $estimators
                ],
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'net_cost',
                'formGroup' => true,
                'rowIdentifier' => 'cost_values',
                'labelName' => 'Net Cost (£)',
                'type' => 'Input\Text',
                'attributes' => ['placeholder' => "£"],
                'class' => 'js-net-cost',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'net_sell',
                'formGroup' => true,
                'rowIdentifier' => 'cost_values',
                'labelName' => 'Net Sell (£)',
                'type' => 'Input\Text',
                'attributes' => ['placeholder' => "£"],
                'class' => 'js-net-sell',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
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
                'name' => 'status',
                'formGroup' => true,
                'rowIdentifier' => 'status_returned_date',
                'labelName' => 'Quote Status *',
                'type' => 'Select',
                'class' => 'js-status',
                'data' => [
                    'select_options' => $statuses,
                    'show_first_option' => false
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'date_returned',
                'formGroup' => true,
                'labelName' => 'Returned Date',
                'rowIdentifier' => 'status_returned_date',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'attributes' => ['autocomplete' => 'off'],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'comments',
                'formGroup' => true,
                'labelName' => 'Comments',
                'type' => 'Textarea',
            ], [
                'name' => 'date_completed',
                'formGroup' => true,
                'labelName' => 'Completion Date',
                'rowIdentifier' => 'completion_fields',
                'type' => 'Input\Text',
                'class' => ['js-date-picker', 'js-date-completed'],
                'attributes' => ['autocomplete' => 'off'],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]

            ], [
                'name' => 'completed_by_id',
                'formGroup' => true,
                'labelName' => 'Completed By ',
                'rowIdentifier' => 'completion_fields',
                'class' => 'js-completed-by-id',
                'type' => 'Select',
                'data' => [
                    'select_options' => $completedUsers
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'tender_status',
                'formGroup' => true,
                'rowIdentifier' => 'completion_fields',
                'labelName' => 'Tender Status *',
                'type' => 'Select',
                'class' => 'js-tender-status',
                'data' => [
                    'select_options' => $tenderStatuses,
                    'show_first_option' => false,
                    'ignore_lock' => $ignoreLockValue
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ],  [
                'name' => 'submit',
                'formGroup' => true,
                'rowIdentifier' => 'button_bar',
                'type' => '\DaveBaker\Form\Block\Button',
                'attributes' => $updateAttrs,
                'data' => [
                    'button_name' => $this->getQuoteHelper()->getActionVerb($this->modelInstance, false) . " Quote",
                    'capabilities' => $this->getQuoteHelper()->getEditCapabilities(),
                    'js_data_items' => [
                        'action' => 'edit'
                    ]
                ],
                'class' => 'js-button-loader btn-block js-action-switcher',
                'formGroupSettings' => [
                    'class' => 'col-md-8'
                ]

            ],
            //  [
            //     'name' => 'create_new_revision_button',
            //     'rowIdentifier' => 'button_bar',
            //     'type' => '\DaveBaker\Form\Block\Button',
            //     'formGroup' => true,
            //     'attributes' => $reviseAttrs,
            //     'data' => [
            //         'button_name' => 'Create New Revision',
            //         'capabilities' => $this->getQuoteHelper()->getEditCapabilities(),
            //         'js_data_items' => [
            //             'action' => 'revise'
            //         ]
            //     ],
            //     'class' => 'btn-block btn-info js-action-switcher',
            //     'formGroupSettings' => [
            //         'class' => 'col-md-4'
            //     ]
            // ],
             [
                'name' => 'delete_button',
                'rowIdentifier' => 'button_bar',
                'type' => '\DaveBaker\Form\Block\Button',
                'formGroup' => true,
                'attributes' => $deleteAttrs,
                'data' => [
                    'button_name' => 'Remove Quote',
                    'capabilities' => $this->getQuoteHelper()->getEditCapabilities(),
                    'js_data_items' => [
                        'type' => 'Quote',
                        'endpoint' => $this->getUrlHelper()->getApiUrl(
                            QuoteDefinition::API_ENDPOINT_DELETE,
                            ['id' => $this->modelInstance->getId()]
                        ),
                        'returnUrl' => $returnUrl
                    ]
                ],
                'class' => 'btn-block btn-danger js-delete-confirm',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'quote_id',
                'type' => 'Input\Hidden',
                'value' => $this->modelInstance->getId()
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ]
        ]);

        $this->createTaskTable();
        $this->createRevisionsTable();

        $isLocked = $this->modelInstance->getTenderStatus() !== QuoteDefinition::TENDER_STATUS_OPEN ||
            $this->modelInstance->getIsDeleted();

        if ($isLocked) {
            if ($this->modelInstance->getIsDeleted()) {
                $message = 'has been removed';
            } else {
                $message = 'is locked';
            }

            $this->addChildBlock(
                $this->createBlock(
                    '\SuttonBaker\Impresario\Block\Form\LargeMessage',
                    "{$this->blockPrefix}.warning.message"
                )->setMessage("This {$this->blockPrefix} " . $message)
                    ->setMessageType($this->modelInstance->getIsDeleted() ? 'danger' : 'warning')
            );
        }

        $this->addChildBlock(array_values($elements));

        // Create the file uploader

        $this->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Upload\TableContainer',
                "{$this->blockPrefix}.file.upload.container"
            )->setOrder('before', "quote.edit.button.bar")
                ->setUploadType($this->modelInstance->getId() ? Upload::TYPE_QUOTE : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY)
                ->setIdentifier($this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession(
                    CoreUploadDefinition::TEMPORARY_PREFIX,
                    Upload::TYPE_QUOTE
                ))
        );


        if ($isLocked || $this->getQuoteHelper()->currentUserCanEdit() == false) {
            $this->lock();
        }
    }

    /**
     * @return $this|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function createTaskTable()
    {
        if (!$this->modelInstance->getId()) {
            return;
        }

        $this->taskTableBlock = $this->createBlock(
            '\SuttonBaker\Impresario\Block\Task\TableContainer',
            "{$this->blockPrefix}.task.table"
        )->setOrder('after', 'quote.edit.project.name.priority')
            ->setCapabilities($this->getTaskHelper()->getViewCapabilities());


        $this->taskTableBlock->setInstanceCollection(
            $this->getQuoteHelper()->getTasksForQuote($this->modelInstance)
        )->setEditLinkParams([
            \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
        ]);

        $this->addChildBlock($this->taskTableBlock);
        return $this;
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function createRevisionsTable()
    {
        if (!$this->modelInstance->getId()) {
            return $this;
        }

        $revisions = $this->getQuoteHelper()->getQuotesForEnquiry(
            $this->modelInstance->getEnquiryId()
        );

        $this->revisionsBlock = $this->createBlock(
            \SuttonBaker\Impresario\Block\Quote\RevisionsTableContainer::class,
            "{$this->blockPrefix}.past.revisions.table"
        )->setOrder('after', 'quote.edit.project.name.priority')
            ->setCapabilities($this->getQuoteHelper()->getViewCapabilities())
            ->setRevisions($revisions)
            ->setQuote($this->modelInstance);


        $this->addChildBlock($this->revisionsBlock);
        return $this;
    }

    /**
     * @return \SuttonBaker\Impresario\Block\Form\Base
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preRender()
    {
        $entityId = $this->getRequest()->getParam(self::ID_KEY);
        $uploadTable = $this->getBlockManager()->getBlock('upload.tile.block');
        $isTemporary = $this->modelInstance->getId() ? false : true;
        $uploadIdentifier = $this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession(
            CoreUploadDefinition::TEMPORARY_PREFIX,
            Upload::TYPE_QUOTE
        );

        $uploadParams = [
            'upload_type' => $this->modelInstance->getId() ? Upload::TYPE_QUOTE : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY,
            'identifier' => $uploadIdentifier
        ];

        if (!$this->isLocked() && $this->getUserHelper()->hasCapability(Roles::CAP_UPLOAD_FILE_ADD)) {
            $uploadTable->addChildBlock(
                $uploadTable->createBlock(
                    '\DaveBaker\Core\Block\Components\FileUploader',
                    "{$this->blockPrefix}.file.uploader",
                    'header_elements'
                )->addJsDataItems(
                    ['endpoint' => $this->getUrlHelper()->getApiUrl(
                        Api::ENDPOINT_FILE_UPLOAD,
                        $uploadParams
                    )]
                )->setActualType(Upload::TYPE_QUOTE)
                    ->setIdentifier($uploadIdentifier)
                    ->setIsTemporary($isTemporary)
            );
        }

        if ($tableBlock = $this->getBlockManager()->getBlock('task.table.list.table')) {
            $tableBlock->removeHeader(['task_id', 'task_type'])
                ->addJsDataItems([
                    Table::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                    $this->getUrlHelper()->getApiUrl(
                        TaskDefinition::API_ENDPOINT_UPDATE_TABLE,
                        [
                            'type' => TaskDefinition::TASK_TYPE_QUOTE,
                            'parent_id' => $this->modelInstance->getId()
                        ]
                    )
                ]);
        }

        $paginator = $this->getBlockManager()->getBlock('task.table.list.paginator')
            ->setRecordsPerPage(TaskDefinition::RECORDS_PER_PAGE_INLINE)
            ->removeClass('pagination-xl')->addClass('pagination-xs');

        if (($tileBlock = $this->getBlockManager()->getBlock('task.table.tile.block')) && !$this->isLocked()) {
            $addButton = $tileBlock->createBlock(
                '\DaveBaker\Core\Block\Html\Tag',
                'create.task.button',
                'header_elements'
            )->setTagText('Create Task')
                ->setTag('a')
                ->addAttribute(['href' => $this->getPageUrl(
                    \SuttonBaker\Impresario\Definition\Page::TASK_EDIT,
                    [
                        'task_type' => \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_QUOTE,
                        'parent_id' => $entityId
                    ],
                    true
                )])
                ->addClass('btn btn-sm btn-primary')
                ->setCapabilities($this->getTaskHelper()->getEditCapabilities());

            $tileBlock->addChildBlock($addButton);
        }

        return parent::_preRender();
    }
}
