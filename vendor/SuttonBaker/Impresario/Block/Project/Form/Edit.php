<?php

namespace SuttonBaker\Impresario\Block\Project\Form;

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
     * @return \DaveBaker\Form\Block\Form|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\SelectConnector\Exception
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
        $projectManagers = $this->createCollectionSelectConnector()
            ->configure(
                $this->getApp()->getHelper('User')->getUserCollection(),
                'ID',
                'user_login'
            )->getElementData();

        // Engineers
        $foremen = $this->createCollectionSelectConnector()
            ->configure(
                $this->getApp()->getHelper('User')->getUserCollection(),
                'ID',
                'user_login'
            )->getElementData();


        // Statuses
        $statuses = $this->createArraySelectConnector()->configure(
            ProjectDefinition::getStatuses()
        )->getElementData();


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
                    'show_first_option' => false
                ],
            ], [
                'name' => 'comments',
                'formGroup' => true,
                'labelName' => 'Comments',
                'type' => 'Textarea',
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => ['button_name' => 'Update Project'],
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

        $this->addChildBlock(array_values($elements));
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function createTaskTableBlock()
    {
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

        if($tableBlock = $this->getBlockManager()->getBlock('task.list.table')){
            $tableBlock->removeHeader(['delete_column', 'status', 'task_id']);
        }

        if($tileBlock = $this->getBlockManager()->getBlock('task.tile.block')) {
            $addButton = $tileBlock->createBlock(
                '\DaveBaker\Core\Block\Html\Tag',
                'create.task.button',
                'header_elements'
            )->setTagText('Create Task')
                ->setTag('a')
                ->addAttribute(['href' => $this->getPageUrl(
                    \SuttonBaker\Impresario\Definition\Page::TASK_EDIT,
                    [
                        'task_type' => \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_PROJECT,
                        'parent_id' => $entityId
                    ],
                    true
                )])
                ->addClass('btn btn-sm btn-primary');

                $tileBlock->addChildBlock($addButton);
        }

        return parent::_preRender();
    }


}