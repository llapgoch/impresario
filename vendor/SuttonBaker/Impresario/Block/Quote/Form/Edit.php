<?php

namespace SuttonBaker\Impresario\Block\Quote\Form;

use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'quote_id';
    const PREFIX_KEY = 'quote';
    const PREFIX_NAME = 'Quote';

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

        $heading = "Create {$prefixName}";
        $editMode = false;

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            $entityInstance = $this->getQuoteHelper()->getQuote($entityId);
            $heading = "Update {$prefixName}";
            $editMode = true;
        }

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Core\Block\Html\Heading', "{$prefixKey}.form.edit.heading")
                ->setHeading($heading)
        );

        $this->addChildBlock($this->getMessagesBlock());


        if($entityId) {
            $this->addChildBlock(
                $this->createBlock('\DaveBaker\Core\Block\Html\Tag', 'create.task')
                    ->setTag('a')
                    ->setTagText('New Task')
                    ->addAttribute(
                        ['href' => $this->getPageUrl(
                            \SuttonBaker\Impresario\Definition\Page::TASK_EDIT,
                            [
                                'task_type' => \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_QUOTE,
                                'parent_id' => $entityId
                            ],
                            $this->getApp()->getHelper('Url')->getCurrentUrl()
                        )]
                    )
            );


            /** @var \SuttonBaker\Impresario\Model\Db\Enquiry\Collection $tasks */
            $taskInstance = $this->getTaskHelper()->getTaskCollectionForEntity(
                $entityId,
                \SuttonBaker\Impresario\Definition\Task::TASK_TYPE_QUOTE,
                \SuttonBaker\Impresario\Definition\Task::STATUS_OPEN
            );

            $taskItems = $taskInstance->load();
            $headers = count($taskItems) ? array_keys($taskItems[0]->getData()) : [];

            $this->addChildBlock(
                $this->createBlock('\DaveBaker\Core\Block\Html\Table', "{$prefixKey}.task.table")
                    ->setHeaders($headers)->setRecords($taskItems)->addEscapeExcludes(
                        ['edit_column', 'delete_column']
                    )
            );
        }


        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit");

        $elements = $builder->build([
            [
                'name' => 'date_received',
                'labelName' => 'Date Received',
                'type' => 'Text',
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly', 'autocomplete' => 'off']
            ], [
                'name' => 'project_name',
                'labelName' => 'Project Name',
                'type' => 'Input\Text'
            ], [
                'name' => 'site_name',
                'labelName' => 'Site Name',
                'type' => 'Input\Text'
            ], [
                'name' => 'client_requested_by',
                'labelName' => 'Client Requested By',
                'type' => 'Input\Text'
            ],[
                'name' => 'client_reference',
                'labelName' => 'Client Reference',
                'type' => 'Input\Text'
            ], [
                'name' => 'date_required',
                'labelName' => 'Required By Date',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'attributes' => [
                    'readonly' => 'readonly',
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '0', 'maxDate' => "+5Y"]
                    )
                ],
            ], [
                'name' => 'project_manager_id',
                'labelName' => 'Project Manager',
                'type' => 'Select',
            ], [
                'name' => 'estimator_id',
                'labelName' => 'Estimator',
                'type' => 'Select',
            ], [
                'name' => 'date_return_by',
                'labelName' => 'Return By Date',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'attributes' => [
                    'readonly' => 'readonly',
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '0', 'maxDate' => "+5Y"]
                    )
                ]
            ], [
                'name' => 'net_cost',
                'labelName' => 'Net Cost (£)',
                'type' => 'Input\Text',
            ], [
                'name' => 'net_sell',
                'labelName' => 'Net Sell (£)',
                'type' => 'Input\Text',
            ], [
                'name' => 'date_returned',
                'labelName' => 'Returned Date',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'attributes' => ['autocomplete' => 'off']

            ],[
                'name' => 'date_completed',
                'labelName' => 'Completion Date',
                'type' => 'Input\Text',
                'class' => 'js-date-picker',
                'attributes' => ['autocomplete' => 'off']

            ],[
                'name' => 'completed_by_id',
                'labelName' => 'Completed By ',
                'type' => 'Select',
            ],[
                'name' => 'status',
                'labelName' => 'Status',
                'type' => 'Select',
            ], [
                'name' => 'comments',
                'labelName' => 'Comments',
                'type' => 'Textarea',
            ], [
                'name' => 'submit',
                'type' => 'Input\Submit',
                'value' => $editMode ? 'Update Quote' : 'Create Quote'
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

        // Set up special values

        // Assigned To
        $projectManagerUsers = $this->getApp()->getHelper('User')->getUserCollection();
        $this->createCollectionSelectConnector()
            ->configure($projectManagerUsers , 'ID', 'user_login', $elements['project_manager_id_element']);

        // Estimator  Users
        $estimatorUsers = $this->getApp()->getHelper('User')->getUserCollection();
        $this->createCollectionSelectConnector()
            ->configure($estimatorUsers, 'ID', 'user_login', $elements['estimator_id_element']);

        // Completed By Users
        $completedByUsers = $this->getApp()->getHelper('User')->getUserCollection();
        $this->createCollectionSelectConnector()
            ->configure($completedByUsers, 'ID', 'user_login', $elements['completed_by_id_element']);


        // Statuses
        $this->createArraySelectConnector()
            ->configure(QuoteDefinition::getStatuses(), $elements['status_element']);

        $elements['status_element']->setShowFirstOption(false);
        $this->addChildBlock(array_values($elements));
    }

}