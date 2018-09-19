<?php

namespace SuttonBaker\Impresario\Block\Quote\Form;

use DaveBaker\Core\Definitions\Api;
use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use SuttonBaker\Impresario\Definition\Upload;
use DaveBaker\Core\Definitions\Upload as CoreUploadDefinition;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'quote_id';
    const PREFIX_KEY = 'quote';
    const PREFIX_NAME = 'Quote';

    /** @var \SuttonBaker\Impresario\Block\Task\TableContainer */
    protected $taskTableBlock;
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

        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $this->addClass('js-quote-form');

        if(!($entityId = $this->getRequest()->getParam(self::ID_KEY))){
            return;
        }

        $this->modelInstance = $this->getQuoteHelper()->getQuote($entityId);
        $projectEntity = $this->getProjectHelper()->getProjectForQuote($entityId);

        // PMs
        if($projectManagers = $this->getRoleHelper()->getProjectManagers()) {
            $projectManagers = $this->createCollectionSelectConnector()
                ->configure(
                    $projectManagers,
                    'ID',
                    'display_name'
                )->getElementData();
        }

        // PMs
        if($estimators = $this->getRoleHelper()->getEstimators()) {
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
                'user_login'
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

        $ignoreLockValue = false;

        if($this->getQuoteHelper()->currentUserCanEdit() && !$this->modelInstance->getIsDeleted()){
            $ignoreLockValue = true;
        }

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit")->setGroupTemplate('form/group-vertical.phtml');

        $deleteAttrs = $projectEntity->getId()
            || $this->modelInstance->getId() == null
            || $this->modelInstance->getIsDeleted() ? ['disabled' => 'disabled'] : [];

        $updateAttrs = $this->modelInstance->getIsDeleted() ? ['disabled' => 'disabled'] : [];

        $returnUrl = $this->getRequest()->getReturnUrl() ?
            $this->getRequest()->getReturnUrl() :
            $this->getUrlHelper()->getPageUrl(Page::QUOTE_LIST);

        $elements = $builder->build([
            [
                'name' => 'date_received',
                'labelName' => 'Date Received *',
                'type' => 'Text',
                'formGroup' => true,
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly', 'autocomplete' => 'off'],
                'rowIdentifier' => 'date_received_row',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'site_name',
                'labelName' => 'Site Name *',
                'formGroup' => true,
                'type' => 'Input\Text'
            ], [
                'name' => 'project_name',
                'formGroup' => true,
                'labelName' => 'Project Name *',
                'type' => 'Input\Text'
            ], [
                'name' => 'client_id',
                'formGroup' => true,
                'rowIdentifier' => 'client_reference_row',
                'labelName' => 'Client',
                'data' => [
                    'select_options' => $clients
                ],
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'client_reference',
                'formGroup' => true,
                'labelName' => 'Client Reference *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'client_reference_row',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
            ], [
                'name' => 'client_requested_by',
                'formGroup' => true,
                'labelName' => 'Client Requested By *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'client_reference_row',
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
                    'readonly' => 'readonly',
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '0', 'maxDate' => "+5Y"]
                    )
                ]
            ], [
                'name' => 'project_manager_id',
                'formGroup' => true,
                'rowIdentifier' => 'project_manager_estimator',
                'labelName' => 'Project Manager *',
                'data' => [
                    'select_options' => $projectManagers
                ],
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'estimator_id',
                'rowIdentifier' => 'project_manager_estimator',
                'formGroup' => true,
                'labelName' => 'Estimator *',
                'data' => [
                    'select_options' => $estimators
                ],
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'net_cost',
                'formGroup' => true,
                'rowIdentifier' => 'cost_values',
                'labelName' => 'Net Cost *',
                'type' => 'Input\Text',
                'attributes' => ['placeholder' => "£"],
                'class' => 'js-net-cost',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'net_sell',
                'formGroup' => true,
                'rowIdentifier' => 'cost_values',
                'labelName' => 'Net Sell *',
                'type' => 'Input\Text',
                'attributes' => ['placeholder' => "£"],
                'class' => 'js-net-sell',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
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
                'name' => 'date_return_by',
                'formGroup' => true,
                'labelName' => 'Return By Date *',
                'rowIdentifier' => 'returned_dates',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'attributes' => [
                    'readonly' => 'readonly',
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '0', 'maxDate' => "+5Y"]
                    )
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'date_returned',
                'formGroup' => true,
                'labelName' => 'Returned Date',
                'rowIdentifier' => 'returned_dates',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'attributes' => ['autocomplete' => 'off'],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],[
                'name' => 'date_completed',
                'formGroup' => true,
                'labelName' => 'Completion Date',
                'rowIdentifier' => 'completion_fields',
                'type' => 'Input\Text',
                'class' => ['js-date-picker', 'js-date-completed'],
                'attributes' => ['autocomplete' => 'off'],
                'data' => [
                    'ignore_lock' => $ignoreLockValue
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]

            ],[
                'name' => 'completed_by_id',
                'formGroup' => true,
                'labelName' => 'Completed By ',
                'rowIdentifier' => 'completion_fields',
                'class' => 'js-completed-by-id',
                'type' => 'Select',
                'data' => [
                    'select_options' => $completedUsers,
                    'ignore_lock' => $ignoreLockValue
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],[
                'name' => 'status',
                'formGroup' => true,
                'labelName' => 'Status *',
                'type' => 'Select',
                'class' => 'js-status',
                'data' => [
                    'select_options' => $statuses,
                    'show_first_option' => false,
                    'ignore_lock' => $ignoreLockValue
                ],
            ], [
                'name' => 'comments',
                'formGroup' => true,
                'labelName' => 'Comments',
                'type' => 'Textarea',
            ], [
                'name' => 'submit',
                'formGroup' => true,
                'rowIdentifier' => 'button_bar',
                'type' => '\DaveBaker\Form\Block\Button',
                'attributes' => $updateAttrs,
                'data' => [
                    'button_name' => $this->getQuoteHelper()->getActionVerb($this->modelInstance) . " Quote",
                    'capabilities' => $this->getQuoteHelper()->getEditCapabilities()
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
                'value' => $entityId
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ], [
                'name' => 'quote_data',
                'type' => 'Input\Hidden',
                'value' => json_encode([
                    'hasProject' => ($projectEntity->getId() ? 1 : 0),
                    'completedStatus' => QuoteDefinition::STATUS_WON
                ]),
                'class' => 'js-quote-data'
            ]
        ]);

        if($entityId) {
            $this->taskTableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\TableContainer',
                "{$prefixKey}.task.table"
            )->setOrder('after', 'quote.edit.project.name.form.group')
                ->setCapabilities($this->getTaskHelper()->getViewCapabilities());

            $this->taskTableBlock->setInstanceCollection(
                $this->getTaskHelper()->getTaskCollectionForEntity(
                    $entityId,
                    TaskDefinition::TASK_TYPE_QUOTE
                )
            )->setEditLinkParams([
                \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
            ]);


            $this->addChildBlock($this->taskTableBlock);
        }

        if(($this->modelInstance->getStatus() !== QuoteDefinition::STATUS_OPEN || $this->modelInstance->getIsDeleted())){
            $this->addChildBlock(
                $this->createBlock(
                    '\SuttonBaker\Impresario\Block\Form\LargeMessage',
                    "{$prefixKey}.warning.message"
                )->setMessage("This {$prefixName} " . ($this->modelInstance->getIsDeleted() ? "has been removed" : "is locked"))
                    ->setMessageType($this->modelInstance->getIsDeleted() ? 'danger' : 'warning')
            );
        }

        $this->addChildBlock(array_values($elements));

        // Create the file uploader

        $this->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Upload\TableContainer',
                "{$prefixKey}.file.upload.container"
            )->setOrder('before', "quote.edit.button.bar")
                ->setUploadType($this->modelInstance->getId() ? Upload::TYPE_QUOTE : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY)
                ->setIdentifier($this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession())
        );


        if(($this->modelInstance->getStatus() !== QuoteDefinition::STATUS_OPEN) ||
            $this->getQuoteHelper()->currentUserCanEdit() == false){
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
            'upload_type' => $this->modelInstance->getId() ? Upload::TYPE_QUOTE : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY,
            'identifier' => $this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession()
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

        if($tableBlock = $this->getBlockManager()->getBlock('task.table.list.table')){
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

        if(($tileBlock = $this->getBlockManager()->getBlock('task.table.tile.block')) && !$this->isLocked()) {
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