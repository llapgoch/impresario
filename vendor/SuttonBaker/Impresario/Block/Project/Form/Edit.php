<?php

namespace SuttonBaker\Impresario\Block\Project\Form;

use DaveBaker\Core\Definitions\Api;
use DaveBaker\Core\Definitions\Table;
use \SuttonBaker\Impresario\Definition\Invoice as InvoiceDefinition;
use \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use DaveBaker\Core\Definitions\Upload as CoreUploadDefinition;
use SuttonBaker\Impresario\Definition\Upload;
use DaveBaker\Core\Definitions\Roles;
use SuttonBaker\Impresario\Definition\Roles as DefinitionRoles;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'project_id';
    const COMPLETION_TEMPORARY_PREFIX = 'proj_comp_tmp';

    protected $prefixName = 'Project';
    protected $blockPrefix = 'project';

    /** @var \SuttonBaker\Impresario\Block\Task\TableContainer */
    protected $taskTableBlock;
    /** @var \SuttonBaker\Impresario\Block\Invoice\TableContainer */
    protected $invoiceTableBlock;
    /** @var \SuttonBaker\Impresario\Block\Cost\TableContainer */
    protected $costTableBlock;
    /** @var \SuttonBaker\Impresario\Block\Variation\TableContainer */
    protected $variationTableBlock;
    /** @var \SuttonBaker\Impresario\Model\Db\Project */
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

        wp_register_script('impresario_calculator', get_template_directory_uri() . '/assets/js/profit-calculator.js', ['jquery']);
        wp_enqueue_script('impresario_calculator');

        wp_enqueue_script('impresario_form_validator');
        $this->addClass('js-validate-form js-form-overlay');
        $editMode = false;

        // Select options for checklist items
        $yesNo = [
            ['value' => 1, 'name' => 'Yes'],
            ['value' => 0, 'name' => 'No'],
        ];

        $yesNoNa = array_merge($yesNo, [['value' => 3, 'name' => 'N/A']]);

        $this->addJsDataItems([
            'endpointValidateSave' => $this->getUrlHelper()->getApiUrl(ProjectDefinition::API_ENDPOINT_VALIDATE_SAVE),
            'endpointSave' => $this->getUrlHelper()->getApiUrl(ProjectDefinition::API_ENDPOINT_SAVE),
            'idElementSelector' => '[name="project_id"]',
            'idKey' => 'project_id'
        ]);

        if ($entityId = $this->getRequest()->getParam(self::ID_KEY)) {
            $this->modelInstance = $this->getProjectHelper()->getProject($entityId);
            $editMode = true;
        }

        // Clients
        $clients = $this->createCollectionSelectConnector()
            ->configure(
                $this->getClientHelper()->getClientCollection(),
                'client_id',
                'client_name'
            )->getElementData();

        // PMs
        if ($projectManagers = $this->getRoleHelper()->getProjectManagers()) {
            $projectManagers = $this->createCollectionSelectConnector()
                ->configure(
                    $projectManagers,
                    'ID',
                    'display_name'
                )->getElementData();
        }

        // Foremen
        if ($foremen = $this->getRoleHelper()->getForemen()) {
            $foremen = $this->createCollectionSelectConnector()
                ->configure(
                    $foremen,
                    'ID',
                    'display_name'
                )->getElementData();
        }

        // Statuses
        $statuses = $this->createArraySelectConnector()->configure(
            ProjectDefinition::getStatuses()
        )->getElementData();

        $ignoreLockValue = false;

        if ($this->getQuoteHelper()->currentUserCanEdit()) {
            $ignoreLockValue = true;
        }

        $clienProjectLocked = $this->getUserHelper()->hasCapability(DefinitionRoles::CAP_EDIT_PROJECT_CLIENT) == false;
        $projectNameReadonly = $clienProjectLocked ? ['readonly' => $clienProjectLocked] : [];

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$this->blockPrefix}_edit")->setGroupTemplate('form/group-vertical.phtml');

        $disabledAttrs = $this->modelInstance->getId() && !$this->modelInstance->isComplete() ? [] : ['disabled' => 'disabled'];
        $updateAttrs = $this->modelInstance->getIsDeleted() ? ['disabled' => 'disabled'] : [];

        $returnUrl = $this->getRequest()->getReturnUrl() ?
            $this->getRequest()->getReturnUrl() : $this->getUrlHelper()->getPageUrl(Page::PROJECT_LIST);

        $this->addRecordMonitorBlock(
            $this->modelInstance,
            $this->getUrlHelper()->getApiUrl(ProjectDefinition::API_ENDPOINT_RECORD_MONITOR)
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
                'attributes' => ['readonly' => 'readonly']
            ], [
                'name' => 'project_name',
                'formGroup' => true,
                'data' => [
                    'locked' => $clienProjectLocked,
                ],
                'labelName' => 'Project Name *',
                'type' => 'Input\Text',
                'attributes' => $projectNameReadonly
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
                        ['minDate' => '0', 'maxDate' => "+5Y"]
                    )
                ]
            ], [
                'name' => 'client_id',
                'labelName' => 'Client *',
                'formGroup' => true,
                'type' => 'Select',
                'rowIdentifier' => 'client_reference_row',
                'data' => [
                    'select_options' => $clients,
                    'locked' => $clienProjectLocked
                ],
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
                'attributes' => ['readonly' => 'readonly']
            ], [
                'name' => 'client_requested_by',
                'formGroup' => true,
                'labelName' => 'Client Requested By *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'client_reference_row',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
                'attributes' => ['readonly' => 'readonly']
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
                'name' => 'project_manager_id',
                'formGroup' => true,
                'rowIdentifier' => 'project_manager_foreman',
                'labelName' => 'Contracts Manager',
                'data' => [
                    'select_options' => $projectManagers
                ],
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'assigned_foreman_id',
                'rowIdentifier' => 'project_manager_foreman',
                'formGroup' => true,
                'labelName' => 'Foreman',
                'data' => [
                    'select_options' => $foremen
                ],
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'client_project_manager',
                'formGroup' => true,
                'labelName' => 'Client Project Manager',
                'type' => 'Input\Text',
                'rowIdentifier' => 'project_manager_foreman',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'total_net_cost',
                'formGroup' => true,
                'rowIdentifier' => 'cost_values_profit',
                'labelName' => 'Net Cost',
                'type' => 'Input\Text',
                'attributes' => [
                    'placeholder' => "£",
                    'readonly' => 'readonly',
                    'data-actual-value' => $this->modelInstance->getTotalNetCost()
                ],
                'class' => 'js-net-cost',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'total_net_sell',
                'formGroup' => true,
                'rowIdentifier' => 'cost_values_profit',
                'labelName' => 'Net Sell',
                'type' => 'Input\Text',
                'attributes' => [
                    'placeholder' => "£",
                    'readonly' => 'readonly',
                    'data-actual-value' => $this->modelInstance->getTotalNetSell()
                ],
                'class' => 'js-net-sell',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'profit',
                'formGroup' => true,
                'rowIdentifier' => 'cost_values_profit',
                'labelName' => 'Profit',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly'],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'amount_invoiced',
                'formGroup' => true,
                'rowIdentifier' => 'cost_values_secondary',
                'labelName' => 'Amount Invoiced',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly'],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'invoice_amount_remaining',
                'formGroup' => true,
                'rowIdentifier' => 'cost_values_secondary',
                'labelName' => 'Invoice Amount Remaining',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly'],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'open_po_remaining',
                'formGroup' => true,
                'rowIdentifier' => 'cost_values_secondary',
                'labelName' => 'Open PO Remaining',
                'type' => 'Input\Text',
                'value' =>  $this->getLocaleHelper()->formatCurrency($this->modelInstance->getOpenPOInvoiceAmountRemaining()),
                'attributes' => ['readonly' => 'readonly'],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ],  [
                'name' => 'total_actual_cost',
                'formGroup' => true,
                'class' => 'js-actual-cost',
                'labelName' => 'Actual Cost',
                'rowIdentifier' => 'actual_cost_values',
                'type' => 'Input\Text',
                'attributes' => [
                    'placeholder' => '£',
                    'readonly' => 'readonly'
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'actual_profit',
                'formGroup' => true,
                'rowIdentifier' => 'actual_cost_values',
                'labelName' => 'Actual Profit',
                'type' => 'Input\Text',
                'attributes' => [
                    'placeholder' => "£",
                    'readonly' => 'readonly',
                    'data-actual-value' => $this->modelInstance->getActualProfit()
                ],
                'class' => 'js-actual-profit',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ],
            ], [
                'name' => 'actual_margin',
                'formGroup' => true,
                'rowIdentifier' => 'actual_cost_values',
                'labelName' => 'Actual Margin',
                'type' => 'Input\Text',
                'attributes' => ['placeholder' => "£", 'readonly' => 'readonly'],
                'class' => 'js-actual-margin',
                'formGroupSettings' => [
                    'class' => 'col-md-4'
                ]
            ], [
                'name' => 'project_start_date',
                'formGroup' => true,
                'labelName' => 'Project Start Date *',
                'rowIdentifier' => 'project_dates',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['maxDate' => "+5Y"]
                    )
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'project_end_date',
                'formGroup' => true,
                'labelName' => 'Project End Date',
                'rowIdentifier' => 'project_dates',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '-5Y', 'maxDate' => "+5Y"]
                    )
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'status',
                'formGroup' => true,
                'labelName' => 'Status *',
                'type' => 'Select',
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
                'name' => 'checklist_plant_off_hired',
                'formGroup' => true,
                'labelName' => 'All Plant Off Hired',
                'rowIdentifier' => 'completion_checklist',
                // Because checkboxes don't work too well in the base system, here's one which works!
                'type' => 'Select',
                'data' => [
                    'select_options' => $yesNo,
                ],
                'class' => 'js-checklist-item',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],
            [
                'name' => 'checklist_cost_invoice_received_logged',
                'formGroup' => true,
                'labelName' => 'All expected cost invoices received and logged',
                'rowIdentifier' => 'completion_checklist',
                // Because checkboxes don't work too well in the base system, here's one which works!
                'type' => 'Select',
                'data' => [
                    'select_options' => $yesNo,
                ],
                'class' => 'js-checklist-item',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],
            [
                'name' => 'checklist_rams_qhse_filing',
                'formGroup' => true,
                'labelName' => 'Completed RAMS sent to QHSE for filing',
                'rowIdentifier' => 'completion_checklist',
                // Because checkboxes don't work too well in the base system, here's one which works!
                'type' => 'Select',
                'data' => [
                    'select_options' => $yesNo,
                ],
                'class' => 'js-checklist-item',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],
            [
                'name' => 'checklist_customer_satisfaction_survey_logged',
                'formGroup' => true,
                'labelName' => 'Customer satisfaction survey logged',
                'rowIdentifier' => 'completion_checklist',
                // Because checkboxes don't work too well in the base system, here's one which works!
                'type' => 'Select',
                'data' => [
                    'select_options' => $yesNoNa,
                ],
                'class' => 'js-checklist-item',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],
            [
                'name' => 'checklist_completion_photos_logged',
                'formGroup' => true,
                'labelName' => 'Completion photos logged',
                'rowIdentifier' => 'completion_checklist',
                // Because checkboxes don't work too well in the base system, here's one which works!
                'type' => 'Select',
                'data' => [
                    'select_options' => $yesNo,
                ],
                'class' => 'js-checklist-item',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],
            [
                'name' => 'checklist_warranty_guarantee_certificate_filed',
                'formGroup' => true,
                'labelName' => 'Any warranty / guarantee certification filed',
                'rowIdentifier' => 'completion_checklist',
                // Because checkboxes don't work too well in the base system, here's one which works!
                'type' => 'Select',
                'data' => [
                    'select_options' => $yesNoNa,
                ],
                'class' => 'js-checklist-item',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],
            [
                'name' => 'checklist_client_advised_operational_maintenance',
                'formGroup' => true,
                'labelName' => 'Client advised on any operational & maintenance requirements',
                'rowIdentifier' => 'completion_checklist',
                // Because checkboxes don't work too well in the base system, here's one which works!
                'type' => 'Select',
                'data' => [
                    'select_options' => $yesNoNa,
                ],
                'class' => 'js-checklist-item',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],
            [
                'name' => 'checklist_client_crm_updated',
                'formGroup' => true,
                'labelName' => 'Client CRM system updated',
                'rowIdentifier' => 'completion_checklist',
                // Because checkboxes don't work too well in the base system, here's one which works!
                'type' => 'Select',
                'data' => [
                    'select_options' => $yesNoNa,
                ],
                'class' => 'js-checklist-item',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],

            [
                'name' => 'submit',
                'formGroup' => true,
                'rowIdentifier' => 'button_bar',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => [
                    'button_name' => $this->getProjectHelper()->getActionVerb($this->modelInstance, false) . " Project",
                    'capabilities' => $this->getProjectHelper()->getEditCapabilities()
                ],
                'attributes' => $updateAttrs,
                'class' => 'btn-block',
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
                    'button_name' => 'Remove Project',
                    'capabilities' => $this->getProjectHelper()->getEditCapabilities(),
                    'js_data_items' => [
                        'type' => 'Project',
                        'endpoint' => $this->getUrlHelper()->getApiUrl(
                            ProjectDefinition::API_ENDPOINT_DELETE,
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
                'name' => 'project_id',
                'type' => 'Input\Hidden',
                'value' => $entityId
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ]
        ]);

        if ($entityId) {
            $this->createTaskTableBlock();
            $this->createInvoiceTableBlock();
            $this->createCostTableBlock();
            $this->createVariationTableBlock();
        }


        if (($this->modelInstance->getStatus() == ProjectDefinition::STATUS_CANCELLED)) {
            $this->addChildBlock(
                $this->createBlock(
                    '\SuttonBaker\Impresario\Block\Form\LargeMessage',
                    "{$this->blockPrefix}.warning.message"
                )->setMessage("This {$this->prefixName} is currently locked")
            );
        }

        $this->addChildBlock(array_values($elements));

        if (
            in_array($this->modelInstance->getStatus(), [ProjectDefinition::STATUS_COMPLETE, ProjectDefinition::STATUS_CANCELLED]) ||
            $this->getProjectHelper()->currentUserCanEdit() == false
        ) {
            $this->lock();
        }

        // Create the file uploader
        $this->addChildBlock(
            $this->createBlock(
                \SuttonBaker\Impresario\Block\Upload\TableContainer::class,
                "{$this->blockPrefix}.file.upload.container"
            )->setOrder('before', "project.edit.button.bar")
                ->setUploadType($this->modelInstance->getId() ? Upload::TYPE_PROJECT : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY)
                ->setIdentifier($this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession(
                    CoreUploadDefinition::TEMPORARY_PREFIX,
                    Upload::TYPE_PROJECT
                ))
                ->setShowDelete(!$this->isLocked())
        );

        // Create the completion certificate file uploader
        $this->addChildBlock(
            $this->createBlock(
                \SuttonBaker\Impresario\Block\Upload\TableContainer::class,
                "{$this->blockPrefix}.file.upload.completion.certificate.container"
            )->setOrder('before', "project.edit.button.bar")
                ->setUploadType($this->modelInstance->getId() ? Upload::TYPE_PROJECT_COMPLETION_CERTIFICATE : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY)
                ->setIdentifier($this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession(
                    self::COMPLETION_TEMPORARY_PREFIX,
                    Upload::TYPE_PROJECT_COMPLETION_CERTIFICATE
                ))
                ->setBlockPrefix('completion.certificate')
                ->setHeading('<strong>Completion</strong> Certificates')
                ->setShowDelete($this->isLocked())
        );


    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function createTaskTableBlock()
    {
        if (!$this->getTaskHelper()->currentUserCanView()) {
            return;
        }

        $this->taskTableBlock = $this->createBlock(
            '\SuttonBaker\Impresario\Block\Task\TableContainer',
            "{$this->blockPrefix}.task.table"
        )->setOrder('after', 'project.edit.project.name.form.group')
            ->setRecordsPerPage(TaskDefinition::RECORDS_PER_PAGE_INLINE);

        $this->taskTableBlock->setInstanceCollection(
            $this->getTaskHelper()->getTaskCollectionForEntity(
                $this->modelInstance->getId(),
                TaskDefinition::TASK_TYPE_PROJECT
            )
        )->setEditLinkParams([
            \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
        ]);


        $this->addChildBlock($this->taskTableBlock);
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function createInvoiceTableBlock()
    {
        if (!$this->getInvoiceHelper()->currentUserCanView()) {
            return;
        }

        $this->invoiceTableBlock = $this->createBlock(
            \SuttonBaker\Impresario\Block\Invoice\TableContainer::class,
            "{$this->blockPrefix}.invoice.table"
        )->setOrder('before', 'project.variation.table');

        $this->invoiceTableBlock->setInstanceCollection(
            $this->getInvoiceHelper()->getInvoiceCollectionForEntity(
                $this->modelInstance->getId(),
                InvoiceDefinition::INVOICE_TYPE_PROJECT
            )
        )->setEditLinkParams([
            \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
        ]);


        $this->addChildBlock($this->invoiceTableBlock);
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function createCostTableBlock()
    {
        if (!$this->getInvoiceHelper()->currentUserCanView()) {
            return;
        }

        $this->costTableBlock = $this->createBlock(
            \SuttonBaker\Impresario\Block\Cost\TableContainer::class,
            "{$this->blockPrefix}.cost.table"
        )->setOrder('before', 'project.variation.table');

        $this->costTableBlock->setInstanceCollection(
            $this->modelInstance->getCosts()
        )->setEditLinkParams([
            \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
        ]);

        $this->addChildBlock($this->costTableBlock);
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function createVariationTableBlock()
    {
        if (!$this->getVariationHelper()->currentUserCanView()) {
            return;
        }

        $this->variationTableBlock = $this->createBlock(
            '\SuttonBaker\Impresario\Block\Variation\TableContainer',
            "{$this->blockPrefix}.variation.table"
        )->setOrder('before', 'project.edit.cost.values.profit');

        $this->variationTableBlock->setInstanceCollection(
            $this->getVariationHelper()->getVariationCollectionForProject(
                $this->modelInstance->getId()
            )
        )->setEditLinkParams([
            \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
        ]);

        $this->addChildBlock($this->variationTableBlock);
    }

    /**
     * @return \SuttonBaker\Impresario\Block\Form\Base
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preRender()
    {
        $entityId = $this->getRequest()->getParam(self::ID_KEY);
        $uploadTable = $this->getBlockManager()->getBlock('upload.tile.block');
        $completionPrefix = 'completion.certificate';
        $completionUploadTable = $this->getBlockManager()->getBlock($completionPrefix . '.tile.block');
        $uploadIdentifier = $this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession(
            CoreUploadDefinition::TEMPORARY_PREFIX,
            Upload::TYPE_PROJECT
        );

        $uploadParams = [
            'upload_type' => $this->modelInstance->getId() ? Upload::TYPE_PROJECT : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY,
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
                )
                    ->setActualType(Upload::TYPE_PROJECT)
                    ->setIdentifier($uploadIdentifier)
            );
        }

        /** Note: the temporary route for projects should never be required (All projects have IDs), here for completeness */
        $completionIdentifier = $this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession(
            self::COMPLETION_TEMPORARY_PREFIX,
            Upload::TYPE_PROJECT_COMPLETION_CERTIFICATE
        );
        
        $uploadCompletionParams = [
            'upload_type' => $this->modelInstance->getId() ? Upload::TYPE_PROJECT_COMPLETION_CERTIFICATE : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY,
            'identifier' => $completionIdentifier
        ];

        if (!$this->isLocked() && $this->getUserHelper()->hasCapability(Roles::CAP_UPLOAD_FILE_ADD)) {
            $completionUploadTable->addChildBlock(
                $completionUploadTable->createBlock(
                    '\DaveBaker\Core\Block\Components\FileUploader',
                    "{$this->blockPrefix}.completion.certificate.file.uploader",
                    'header_elements'
                )->addJsDataItems(
                    [
                        'endpoint' => $this->getUrlHelper()->getApiUrl(
                            Api::ENDPOINT_FILE_UPLOAD,
                            $uploadCompletionParams,
                        ),
                        'blockPrefix' => $completionPrefix
                    ]
                )
                    ->setActualType(Upload::TYPE_PROJECT_COMPLETION_CERTIFICATE)
                    ->setIdentifier($completionIdentifier)
            );
        }

        if ($tableBlock = $this->getBlockManager()->getBlock('task.table.list.table')) {
            $tableBlock->removeHeader(['task_id', 'task_type'])
                ->addJsDataItems([
                    Table::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                    $this->getUrlHelper()->getApiUrl(
                        TaskDefinition::API_ENDPOINT_UPDATE_TABLE,
                        [
                            'type' => TaskDefinition::TASK_TYPE_PROJECT,
                            'parent_id' => $this->modelInstance->getId()
                        ]

                    ),
                ]);
        }

        $paginator = $this->getBlockManager()->getBlock('task.table.list.paginator')
            ->setRecordsPerPage(TaskDefinition::RECORDS_PER_PAGE_INLINE)
            ->removeClass('pagination-xl')->addClass('pagination-xs');

        if ($this->isLocked() == false) {
            if (($taskTileBlock = $this->getBlockManager()->getBlock('task.table.tile.block'))) {
                $taskTileBlock->addChildBlock(
                    $this->createSmallButtonElement(
                        'Create Task',
                        $this->getPageUrl(
                            \SuttonBaker\Impresario\Definition\Page::TASK_EDIT,
                            [
                                'task_type' => \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_PROJECT,
                                'parent_id' => $entityId
                            ],
                            true
                        ),
                        'create.task.button',
                        'header_elements'
                    )->setCapabilities($this->getTaskHelper()->getEditCapabilities())
                );
            }

            if ($variationTileBlock = $this->getBlockManager()->getBlock('variation.tile.block')) {
                $buttonContainer = $variationTileBlock->createBlock(
                    \DaveBaker\Core\Block\Block::class,
                    "{$this->getBlockPrefix()}.variation.button.container",
                    'header_elements'
                );

                $buttonContainer->addChildBlock(
                    $this->createSmallButtonElement(
                        'Create Variation',
                        $this->getPageUrl(
                            \SuttonBaker\Impresario\Definition\Page::VARIATION_EDIT,
                            [
                                'project_id' => $entityId
                            ],
                            true
                        ),
                        'create.variation.button',
                    )->setCapabilities($this->getVariationHelper()->getEditCapabilities())
                );


                $buttonContainer->addChildBlock(
                    $this->createSmallButtonElement(
                        '<span class="fa fa-download" aria-hidden="true"></span>',
                        $this->getPageUrl(
                            \SuttonBaker\Impresario\Definition\Page::PROJECT_VARIATION_INVOICE_DOWNLOAD,
                            [
                                'project_id' => $entityId
                            ],
                            true
                        ),
                        'download.variation.button',
                    )->setCapabilities($this->getVariationHelper()->getEditCapabilities())
                );

                $variationTileBlock->addChildBlock($buttonContainer);
            }

            if ($invoiceTileBlock = $this->getBlockManager()->getBlock('invoice.tile.block')) {
                $buttonContainer = $invoiceTileBlock->createBlock(
                    \DaveBaker\Core\Block\Block::class,
                    "{$this->getBlockPrefix()}.invoice.button.container",
                    'header_elements'
                );
                $buttonContainer->addChildBlock(
                    $this->createSmallButtonElement(
                        'Create Sales Invoice',
                        $this->getPageUrl(
                            \SuttonBaker\Impresario\Definition\Page::INVOICE_EDIT,
                            [
                                'invoice_type' => InvoiceDefinition::INVOICE_TYPE_PROJECT,
                                'parent_id' => $entityId
                            ],
                            true
                        ),
                        'create.invoice.button',
                    )->setCapabilities($this->getInvoiceHelper()->getEditCapabilities())
                );

                $buttonContainer->addChildBlock(
                    $this->createSmallButtonElement(
                        '<span class="fa fa-download" aria-hidden="true"></span>',
                        $this->getPageUrl(
                            \SuttonBaker\Impresario\Definition\Page::PROJECT_SALES_INVOICE_DOWNLOAD,
                            [
                                'project_id' => $entityId
                            ],
                            true
                        ),
                        'download.invoice.button',
                    )->setCapabilities($this->getInvoiceHelper()->getEditCapabilities())
                );

                $invoiceTileBlock->addChildBlock($buttonContainer);
            }

            if ($costTileBlock = $this->getBlockManager()->getBlock('cost.tile.block')) {
                $buttonContainer = $costTileBlock->createBlock(
                    \DaveBaker\Core\Block\Block::class,
                    "{$this->getBlockPrefix()}.cost.button.container",
                    'header_elements'
                );

                $buttonContainer->addChildBlock(
                    $this->createSmallButtonElement(
                        'Create Purchase Order',
                        $this->getPageUrl(
                            \SuttonBaker\Impresario\Definition\Page::COST_EDIT,
                            [
                                'cost_type' => CostDefinition::COST_TYPE_PROJECT,
                                'parent_id' => $entityId
                            ],
                            true
                        ),
                        'create.cost.button',
                    )->setCapabilities($this->getCostHelper()->getEditCapabilities())
                );

                $buttonContainer->addChildBlock(
                    $this->createSmallButtonElement(
                        '<span class="fa fa-download" aria-hidden="true"></span>',
                        $this->getPageUrl(
                            \SuttonBaker\Impresario\Definition\Page::PROJECT_COST_INVOICE_DOWNLOAD,
                            [
                                'project_id' => $entityId
                            ],
                            true
                        ),
                        'download.cost.button',
                    )->setCapabilities($this->getCostHelper()->getEditCapabilities())
                );

                $costTileBlock->addChildBlock($buttonContainer);
            }
        }

        return parent::_preRender();
    }
}
