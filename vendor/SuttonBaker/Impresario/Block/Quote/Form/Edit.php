<?php

namespace SuttonBaker\Impresario\Block\Quote\Form;

use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;

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

        $enquiryItem = $this->getApp()->getRegistry()->get('enquiry_item');

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            $entityInstance = $this->getQuoteHelper()->getQuote($entityId);
            $editMode = true;
        }

        // PMs
        $projectManagers = $this->createCollectionSelectConnector()
            ->configure(
                $this->getApp()->getHelper('User')->getUserCollection(),
                'ID',
                'user_login'
            )->getElementData();

        // Engineers
        $estimators = $this->createCollectionSelectConnector()
            ->configure(
                $this->getApp()->getHelper('User')->getUserCollection(),
                'ID',
                'user_login'
            )->getElementData();

        // Completed Users
        $completedUsers = $this->createCollectionSelectConnector()
            ->configure(
                $this->getApp()->getHelper('User')->getUserCollection(),
                'ID',
                'user_login'
            )->getElementData();

        // Statuses
        $statuses = $this->createArraySelectConnector()->configure(
            QuoteDefinition::getStatuses()
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
                ],
                'value' => $enquiryItem->getId() ? $this->getApp()->getHelper('Date')->utcDbDateToShortLocalOutput($enquiryItem->getDateReceived()) : ''
            ], [
                'name' => 'site_name',
                'labelName' => 'Site Name *',
                'formGroup' => true,
                'type' => 'Input\Text',
                'value' => $enquiryItem->getSiteName()
            ], [
                'name' => 'project_name',
                'formGroup' => true,
                'labelName' => 'Project Name *',
                'type' => 'Input\Text'
            ], [
                'name' => 'client_reference',
                'formGroup' => true,
                'labelName' => 'Client Reference *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'client_reference_row',
                'value' => $enquiryItem->getClientReference(),
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
            ], [
                'name' => 'client_requested_by',
                'formGroup' => true,
                'labelName' => 'Client Requested By *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'client_reference_row',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],

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
                ],
                'value' => $enquiryItem->getProjectManagerId()
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
                'class' => 'js-date-picker',
                'attributes' => ['autocomplete' => 'off'],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]

            ],[
                'name' => 'completed_by_id',
                'formGroup' => true,
                'labelName' => 'Completed By ',
                'rowIdentifier' => 'completion_fields',
                'type' => 'Select',
                'data' => [
                    'select_options' => $completedUsers
                ],
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ]
            ],[
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
                'data' => ['button_name' => $editMode ? 'Update Quote' : 'Create Quote'],
                'class' => 'btn-block'
            ], [
                'name' => 'quote_id',
                'type' => 'Input\Hidden',
                'value' => $entityId
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ]
        ]);

        if($entityId) {
            $this->taskTableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\TableContainer',
                "{$prefixKey}.task.table"
            )->setOrder('after', 'quote.edit.project.name.form.group');

            $this->taskTableBlock->setInstanceCollection(
                $this->getTaskHelper()->getTaskCollectionForEntity(
                    $entityId,
                    TaskDefinition::TASK_TYPE_QUOTE,
                    TaskDefinition::STATUS_OPEN
                )
            )->setEditLinkParams([
                \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
            ]);


            $this->addChildBlock($this->taskTableBlock);
        }

        $this->addChildBlock(array_values($elements));
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
                        'task_type' => \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_QUOTE,
                        'parent_id' => $entityId
                    ],
                    true
                )])
                ->addClass('btn btn-sm btn-primary');

                $tileBlock->addChildBlock($addButton);
        }

        return parent::_preRender(); // TODO: Change the autogenerated stub
    }


}