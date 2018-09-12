<?php

namespace SuttonBaker\Impresario\Block\Project\Form;

use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Api\Project;
use \SuttonBaker\Impresario\Definition\Invoice as InvoiceDefinition;
use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'project_id';
    const PREFIX_KEY = 'project';
    const PREFIX_NAME = 'Project';

    /** @var \SuttonBaker\Impresario\Block\Task\TableContainer */
    protected $taskTableBlock;
    /** @var \SuttonBaker\Impresario\Block\Invoice\TableContainer */
    protected $invoiceTableBlock;
    /** @var \SuttonBaker\Impresario\Block\Variation\TableContainer */
    protected $variationTableBlock;
    /** @var \SuttonBaker\Impresario\Model\Db\Project */
    protected $modelInstance;

    /**
     * @return \SuttonBaker\Impresario\Block\Form\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\SelectConnector\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function _preDispatch()
    {
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;
        $editMode = false;

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
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
        if($projectManagers = $this->getRoleHelper()->getProjectManagers()) {
            $projectManagers = $this->createCollectionSelectConnector()
                ->configure(
                    $projectManagers,
                    'ID',
                    'display_name'
                )->getElementData();
        }

        // Foremen
        if($foremen = $this->getRoleHelper()->getForemen()) {
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

        if($this->getQuoteHelper()->currentUserCanEdit()){
            $ignoreLockValue = true;
        }

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit")->setGroupTemplate('form/group-vertical.phtml');

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
                'labelName' => 'Client *',
                'formGroup' => true,
                'type' => 'Select',
                'rowIdentifier' => 'client_reference_row',
                'data' => [
                    'select_options' => $clients
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
                'name' => 'project_manager_id',
                'formGroup' => true,
                'rowIdentifier' => 'project_manager_foreman',
                'labelName' => 'Project Manager *',
                'data' => [
                    'select_options' => $projectManagers
                ],
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'assigned_foreman_id',
                'rowIdentifier' => 'project_manager_foreman',
                'formGroup' => true,
                'labelName' => 'Foreman *',
                'data' => [
                    'select_options' => $foremen
                ],
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'actual_cost',
                'formGroup' => true,
                'labelName' => 'Actual Cost *',
                'rowIdentifier' => 'cost_values',
                'type' => 'Input\Text',
                'formGroupSettings' => [
                    'class' => 'col-md-3'
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
                    'class' => 'col-md-3'
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
                    'class' => 'col-md-3'
                ]
            ], [
                'name' => 'profit',
                'formGroup' => true,
                'rowIdentifier' => 'cost_values',
                'labelName' => 'Profit',
                'type' => 'Input\Text',
                'attributes' => ['disabled' => 'disabled'],
                'class' => 'js-profit-calculate',
                'formGroupSettings' => [
                    'class' => 'col-md-3'
                ]
            ], [
                'name' => 'project_start_date',
                'formGroup' => true,
                'labelName' => 'Project Start Date *',
                'rowIdentifier' => 'project_dates',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'attributes' => [
                    'readonly' => 'readonly',
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '-2W', 'maxDate' => "+5Y"]
                    )
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ], [
                'name' => 'project_end_date',
                'formGroup' => true,
                'labelName' => 'Project End Date *',
                'rowIdentifier' => 'project_dates',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'attributes' => [
                    'autocomplete' => 'off',
                    'readonly' => 'readonly',
                    'data-date-settings' => json_encode(
                        ['minDate' => '0', 'maxDate' => "+5Y"]
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
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => [
                    'button_name' => 'Update Project',
                    'capabilities' => $this->getEnquiryHelper()->getEditCapabilities()
                ],
                'class' => 'btn-block'
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

        if($entityId) {
            $this->createTaskTableBlock();
            $this->createInvoiceTableBlock();
            $this->createVariationTableBlock();
        }


        if(($this->modelInstance->getStatus() !== ProjectDefinition::STATUS_OPEN)){
            $this->addChildBlock(
                $this->createBlock(
                    '\SuttonBaker\Impresario\Block\Form\LargeMessage',
                    "{$prefixKey}.warning.message"
                )->setMessage("This {$prefixName} is currently locked")
            );
        }

        $this->addChildBlock(array_values($elements));

        if(($this->modelInstance->getStatus() !== ProjectDefinition::STATUS_OPEN) ||
            $this->getEnquiryHelper()->currentUserCanEdit() == false){
            $this->lock();
        }
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function createTaskTableBlock()
    {
        if(!$this->getTaskHelper()->currentUserCanView()){
            return;
        }

        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $this->taskTableBlock = $this->createBlock(
            '\SuttonBaker\Impresario\Block\Task\TableContainer',
            "{$prefixKey}.task.table"
        )->setOrder('after', 'project.edit.project.name.form.group');

        $this->taskTableBlock->setInstanceCollection(
            $this->getTaskHelper()->getTaskCollectionForEntity(
                $this->modelInstance->getId(),
                TaskDefinition::TASK_TYPE_PROJECT,
                TaskDefinition::STATUS_OPEN
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
     */
    protected function createInvoiceTableBlock()
    {
        if(!$this->getInvoiceHelper()->currentUserCanView()){
            return;
        }
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $this->invoiceTableBlock = $this->createBlock(
            '\SuttonBaker\Impresario\Block\Invoice\TableContainer',
            "{$prefixKey}.invoice.table"
        )->setOrder('after', 'project.edit.project.name.form.group');

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
     */
    protected function createVariationTableBlock()
    {
        if(!$this->getVariationHelper()->currentUserCanView()){
            return;
        }
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $this->variationTableBlock = $this->createBlock(
            '\SuttonBaker\Impresario\Block\Variation\TableContainer',
            "{$prefixKey}.variation.table"
        )->setOrder('after', 'project.edit.project.name.form.group');

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
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preRender()
    {

        $entityId = $this->getRequest()->getParam(self::ID_KEY);
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        if($tableBlock = $this->getBlockManager()->getBlock('task.table.list.table')) {
            $tableBlock->removeHeader(['status', 'task_id', 'task_type'])
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

        if($taskTileBlock = $this->getBlockManager()->getBlock('task.table.tile.block')) {
            $taskTileBlock->addChildBlock(
                $this->createSmallButtonElement('Create Task', $this->getPageUrl(
                \SuttonBaker\Impresario\Definition\Page::TASK_EDIT, [
                    'task_type' => \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_PROJECT,
                    'parent_id' => $entityId],
                true
                    ), 'create.task.button', 'header_elements'
                )->setCapabilities($this->getTaskHelper()->getEditCapabilities())
            );
        }

        if($variationTileBlock = $this->getBlockManager()->getBlock('variation.tile.block')) {
            $variationTileBlock->addChildBlock(
                $this->createSmallButtonElement('Create Variation', $this->getPageUrl(
                \SuttonBaker\Impresario\Definition\Page::VARIATION_EDIT, [
                    'project_id' => $entityId],
                true
                    ), 'create.variation.button', 'header_elements'
                )->setCapabilities($this->getVariationHelper()->getEditCapabilities())
            );
        }

        if($invoiceTileBlock = $this->getBlockManager()->getBlock('invoice.tile.block')) {
            $invoiceTileBlock->addChildBlock(
                $this->createSmallButtonElement('Create Invoice', $this->getPageUrl(
                \SuttonBaker\Impresario\Definition\Page::INVOICE_EDIT, [
                    'invoice_type' => InvoiceDefinition::INVOICE_TYPE_PROJECT,
                    'parent_id' => $entityId],
                true
                    ), 'create.invoice.button', 'header_elements'
                )->setCapabilities($this->getInvoiceHelper()->getEditCapabilities())
            );
        }

        return parent::_preRender();
    }


}