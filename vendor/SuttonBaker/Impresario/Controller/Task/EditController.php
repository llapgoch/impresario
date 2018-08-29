<?php

namespace SuttonBaker\Impresario\Controller\Task;

use DaveBaker\Core\Definitions\Messages;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;

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
        $taskType = $this->getRequest()->getParam(self::TASK_TYPE_PARAM);
        $parentId = $this->getRequest()->getParam(self::PARENT_ID_PARAM);
        $instanceId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM);
        $parentItem = null;

        $modelInstance = $this->getTaskHelper()->getTask();

        if($instanceId){
            // We're loading, fellas!
            $modelInstance->load($instanceId);

            if(!$modelInstance->getId() || $modelInstance->getIsDeleted()){
                $this->addMessage('The task could not be found', Messages::ERROR);
                return $this->getResponse()->redirectReferer();
            }

        }else {
            if (!$this->getTaskHelper()->isValidTaskType($taskType)) {
                $this->addMessage('Invalid Task Type');
                $this->getResponse()->redirectReferer();
            }
        }

        $parentItem = $this->getParentItem($modelInstance);
        $this->parentItem = $parentItem;
        $this->taskType = $this->getTaskHelper()->getTaskTypeForParent($parentItem);

        if(!$parentItem || !$parentItem->getId()){
            $this->addMessage('The parent item of the task could not be found');
            return $this->getResponse()->redirectReferer();
        }


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

            // Don't save a completed user if status isn't completed
            if (isset($postParams['status'])) {
                if ($postParams['status'] !== \SuttonBaker\Impresario\Definition\Task::STATUS_COMPLETE) {
                    unset($postParams['date_completed']);
                    unset($postParams['completed_by_id']);
                }
            }

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
            $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::TASK_LIST);
        }



        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Apply the values to the form element
        if($modelInstance->getId()) {
            $data = $modelInstance->getData();

            if($modelInstance->getTargetDate()){
                $data['target_date'] = $helper->utcDbDateToShortLocalOutput($modelInstance->getTargetDate());
            }

            if($modelInstance->getDateCompleted()){
                $data['date_completed'] = $helper->utcDbDateToShortLocalOutput($modelInstance->getDateCompleted());
            }

            $applicator->configure(
                $this->editForm,
                $data
            );
        }
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

        // Add created by user
        if(!$data['task_id']) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
        $data['parent_id'] = $this->parentItem->getId();
        $data['task_type'] = $this->taskType;

        /** @var \SuttonBaker\Impresario\Model\Db\Task $modelInstance */
        $modelInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Task');
        $this->addMessage("The task has been " . ($data['task_id'] ? 'updated' : 'added'));
        $modelInstance->setData($data)->save();
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
        )->setOrder('after', 'task.form.edit.heading')->addErrors($validator->getErrors());

        $this->editForm->addChildBlock($errorBlock);

        // Sets the values back onto the form element
        $applicator->configure(
            $this->editForm,
            $this->getRequest()->getPostParams()
        );
    }
}