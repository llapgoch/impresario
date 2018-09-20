<?php

namespace SuttonBaker\Impresario\Block\Task\Form;

use DaveBaker\Core\Definitions\Api;
use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use SuttonBaker\Impresario\Definition\Upload;
use DaveBaker\Core\Definitions\Upload as CoreUploadDefinition;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'task_id';
    const PREFIX_KEY = 'task';
    const PREFIX_NAME = 'Task';

    /** @var \SuttonBaker\Impresario\Model\Db\Task */
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
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $heading = "Create {$prefixName}";
        $editMode = false;

        $this->modelInstance = $this->getApp()->getRegistry()->get('model_instance');
        $parentItem = $this->getApp()->getRegistry()->get('parent_item');
        $taskType = $this->getApp()->getRegistry()->get('task_type');

        if($this->modelInstance->getId()){
            $editMode = true;
        }

        // PMs
        $assignedToUsers = $this->createCollectionSelectConnector()
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
            TaskDefinition::getStatuses()
        )->getElementData();

        // Priorities
        $priorities = $this->createArraySelectConnector()->configure(
            TaskDefinition::getPriorities()
        )->getElementData();

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit")->setGroupTemplate('form/group-vertical.phtml');

        $disabledAttrs = $this->modelInstance->getId() ? [] : ['disabled' => 'disabled'];
        $returnUrl = $this->getRequest()->getReturnUrl() ?
            $this->getRequest()->getReturnUrl() :
            $this->getUrlHelper()->getPageUrl(Page::TASK_LIST);

        $elements = $builder->build([
            [
                'name' => 'description',
                'labelName' => 'Description *',
                'type' => 'Textarea',
                'formGroup' => true
            ], [
                'name' => 'assigned_to_id',
                'labelName' => 'Assigned To *',
                'type' => 'Select',
                'rowIdentifier' => 'assigned_target_date',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'data' => [
                    'select_options' => $assignedToUsers
                ],
            ], [
                'name' => 'target_date',
                'labelName' => 'Target Date *',
                'class' => 'js-date-picker',
                'rowIdentifier' => 'assigned_target_date',
                'type' => 'Input\Text',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'value' => $this->getApp()->getHelper('Date')->utcDbDateToShortLocalOutput($parentItem->getTargetDate()),
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(['minDate' => '', 'maxDate' => "+5Y"])
                ],
            ], [
                'name' => 'notes',
                'labelName' => 'Notes',
                'type' => 'Textarea',
                'formGroup' => true
            ], [
                'name' => 'priority',
                'rowIdentifier' => 'priority_status',
                'labelName' => 'Priority *',
                'formGroup' => true,
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'data' => [
                    'select_options' => $priorities,
                    'show_first_option' => false
                ],

            ],[
                'name' => 'status',
                'rowIdentifier' => 'priority_status',
                'labelName' => 'Status *',
                'formGroup' => true,
                'type' => 'Select',
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'data' => [
                    'select_options' => $statuses,
                    'show_first_option' => false

                ]
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'rowIdentifier' => 'button_bar',
                'formGroup' => true,
                'data' => [
                    'button_name' => $this->getTaskHelper()->getActionVerb($this->modelInstance) . " Task",
                    'capabilities' => $this->getTaskHelper()->getEditCapabilities()
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
                'attributes' => $disabledAttrs,
                'data' => [
                    'button_name' => 'Remove Task',
                    'capabilities' => $this->getTaskHelper()->getEditCapabilities(),
                    'js_data_items' => [
                        'type' => 'Task',
                        'endpoint' => $this->getUrlHelper()->getApiUrl(
                            TaskDefinition::API_ENDPOINT_DELETE,
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
                'name' => 'task_id',
                'type' => 'Input\Hidden',
                'value' => $this->modelInstance->getId()
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ]
        ]);

        $this->addChildBlock(array_values($elements));

        // Create the file uploader
        $this->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Upload\TableContainer',
                "{$prefixKey}.file.upload.container"
            )->setOrder('before', "task.edit.button.bar")
                ->setUploadType($this->modelInstance->getId() ? Upload::TYPE_TASK : CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY)
                ->setIdentifier($this->modelInstance->getId() ? $this->modelInstance->getId() : $this->getUploadHelper()->getTemporaryIdForSession())
        );

        if($this->getTaskHelper()->currentUserCanEdit() == false){
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
            'upload_type' => $this->modelInstance->getId() ? Upload::TYPE_TASK: CoreUploadDefinition::UPLOAD_TYPE_TEMPORARY,
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
        return parent::_preRender();
    }

}