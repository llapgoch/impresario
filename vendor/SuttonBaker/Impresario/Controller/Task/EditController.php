<?php

namespace SuttonBaker\Impresario\Controller\Task;

use DaveBaker\Core\Definitions\Messages;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use SuttonBaker\Impresario\Installer\Task;

/**
 * Class EditController
 * @package SuttonBaker\Impresario\Controller\Task
 */
class EditController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const TASK_TYPE_PARAM = 'task_type';
    const PARENT_ID_PARAM = 'parent_id';
    const ENTITY_ID_PARAM = 'task_id';

    /** @var \DaveBaker\Form\Block\Form $editForm */
    protected $editForm;
    protected $parentItem;
    protected $taskType;
    protected $modelInstance;

    protected $nonUserValues = [
        'task_id',
        'created_by_id',
        'last_edited_by_id',
        'task_type',
        'parent_id',
        'created_at',
        'updated_at',
        'is_deleted'
    ];

    /**
     * @return \DaveBaker\Core\App\Response|object|\SuttonBaker\Impresario\Controller\Base
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _preDispatch()
    {

        // Set instance values before the blocks are created
        $taskType = $this->getRequest()->getParam(self::TASK_TYPE_PARAM);
        $parentId = $this->getRequest()->getParam(self::PARENT_ID_PARAM);
        $instanceId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM);

        $this->setModelInstance($this->getTaskHelper()->getTask());

        if(!$instanceId){
            $this->modelInstance->setTaskType($this->taskType);
            $this->modelInstance->setParentId($parentId);
        }

        if($instanceId){
            // We're loading, fellas!
            $this->modelInstance->load($instanceId);

            if(!$this->modelInstance->getId() || $this->modelInstance->getIsDeleted()){
                $this->addMessage('The task could not be found', Messages::ERROR);
                return $this->getResponse()->redirectReferer();
            }

        }else {
            if (!$this->getTaskHelper()->isValidTaskType($taskType)) {
                $this->addMessage('Invalid Task Type');
                $this->getResponse()->redirectReferer();
            }
        }

        $this->setParentItem($this->getParentItem($this->modelInstance));
        $this->setTaskType($this->getTaskHelper()->getTaskTypeForParent($this->parentItem));

        if(!$this->parentItem || !$this->parentItem->getId()){
            $this->addMessage('The parent item of the task could not be found');
            return $this->getResponse()->redirectReferer();
        }

        if(!$this->taskType){
            $this->addMessage('Invalid parent type');
            return $this->getResponse()->redirectReferer();
        }
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    public function execute()
    {
        if(!($this->editForm = $this->getApp()->getBlockManager()->getBlock('task.form.edit'))){
            return;
        }

        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        // Form submission
        if($this->getRequest()->getPostParam('action')){
            $postParams = $this->getRequest()->getPostParams();

            // Convert dates to DB
            if (isset($postParams['date_completed'])){
                $postParams['date_completed'] = $helper->localDateToDb($postParams['date_completed']);
            }

            if(isset($postParams['target_date'])){
                $postParams['target_date'] = $helper->localDateToDb($postParams['target_date']);
            }

            /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
            $configurator = $this->createAppObject('\SuttonBaker\Impresario\Form\TaskConfigurator');

            /** @var \DaveBaker\Form\Validation\Validator $validator */
            $validator = $this->createAppObject('\DaveBaker\Form\Validation\Validator')
                ->setValues($postParams)
                ->configurate($configurator);

            if(!$validator->validate()){
                return $this->prepareFormErrors($validator);
            }

            $this->saveFormValues($postParams);

            if(!$this->getApp()->getResponse()->redirectToReturnUrl()) {
                $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::TASK_LIST);
            }
        }

        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Apply the values to the form element
        if($this->modelInstance->getId()) {
            $data = $this->modelInstance->getData();

            if($this->modelInstance->getTargetDate()){
                $data['target_date'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getTargetDate());
            }

            if($this->modelInstance->getDateCompleted()){
                $data['date_completed'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateCompleted());
            }

            $applicator->configure(
                $this->editForm,
                $data
            );
        }
    }


    /**
     * @param $modelInstance
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function setModelInstance($modelInstance)
    {
        $this->modelInstance = $modelInstance;
        $this->getApp()->getRegistry()->register('model_instance', $modelInstance);
    }

    /**
     * @param $parentItem
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function setParentItem($parentItem)
    {
        $this->parentItem = $parentItem;
        $this->getApp()->getRegistry()->register('parent_item', $parentItem);
    }

    /**
     * @param string $taskType
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function setTaskType($taskType)
    {
        $this->taskType = $taskType;
        $this->getApp()->getRegistry()->register('task_type', $taskType);
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Task $instance
     * @return \SuttonBaker\Impresario\Model\Db\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getParentItem(\SuttonBaker\Impresario\Model\Db\Task $instance)
    {
        $taskType = null;
        $parentId = null;


        if($instance->getId()){
            $taskType = $instance->getTaskType();
            $parentId = $instance->getParentId();
        }else{
            $taskType = $this->getRequest()->getParam(self::TASK_TYPE_PARAM);
            $parentId = $this->getRequest()->getParam(self::PARENT_ID_PARAM);
        }

        if($taskType == TaskDefinition::TASK_TYPE_ENQUIRY){
            return $this->getEnquiryHelper()->getEnquiry($parentId);
        }

        if($taskType == TaskDefinition::TASK_TYPE_QUOTE){
            return $this->getQuoteHelper()->getQuote($parentId);
        }
    }

    /**
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function saveFormValues($data)
    {
        if(!$this->getApp()->getHelper('User')->isLoggedIn()){
            return;
        }

        foreach($this->nonUserValues as $nonUserValue){
            if(isset($data[$nonUserValue])){
                unset($data[$nonUserValue]);
            }
        }

        // Add created by user
        if(!$this->modelInstance->getTaskId()) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
            $data['task_type'] = $this->taskType;
            $data['parent_id'] = $this->parentItem->getId();
        }

        // Only set the completed date when the status changes from open to complete
        if($data['status'] == TaskDefinition::STATUS_COMPLETE &&
            $this->modelInstance->getStatus() !== TaskDefinition::STATUS_COMPLETE){

            $data['date_completed'] = $this->getDateHelper()->utcTimestampToDb();
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        $this->modelInstance->setData($data)->save();
        $this->addMessage(
            "The task has been " . ($this->modelInstance->getId() ? 'updated' : 'added'),
            Messages::SUCCESS
        );

        return $this;
    }

    /**
     * @param \DaveBaker\Form\Validation\Validator $validator
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     */
    protected function prepareFormErrors(
        \DaveBaker\Form\Validation\Validator $validator
    ) {
        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Create main error block
        /** @var \DaveBaker\Form\Block\Error\Main $errorBlock */
        $errorBlock = $this->getApp()->getBlockManager()->createBlock(
            '\DaveBaker\Form\Block\Error\Main',
            'task.edit.form.errors'
        )->setOrder('before', '')->addErrors($validator->getErrors());

        $this->editForm->addChildBlock($errorBlock);

        // Sets the values back onto the form element
        $applicator->configure(
            $this->editForm,
            $this->getRequest()->getPostParams(),
            $validator
        );
    }
}