<?php

namespace SuttonBaker\Impresario\Api;
use DaveBaker\Core\Api\Exception;
use DaveBaker\Core\Block\Components\Paginator;
use DaveBaker\Core\Definitions\Messages;
use DaveBaker\Form\Block\Error\Main;
use DaveBaker\Form\Validation\Validator;
use SuttonBaker\Impresario\Block\Table\StatusLink;
use SuttonBaker\Impresario\Definition\Page;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
use SuttonBaker\Impresario\Definition\Task as TaskDefinition;
use SuttonBaker\Impresario\Form\ProjectConfigurator;
use SuttonBaker\Impresario\SaveConverter\Project as ProjectConverter;

/**
 * Class Project
 * @package SuttonBaker\Impresario\Api
 *
 */
class Project
    extends Base
{
    /** @var string  */
    protected $blockPrefix = 'project';
    /** @var array  */
    protected $capabilities = [Roles::CAP_VIEW_PROJECT];

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function validatesaveAction(
        $params,
        \WP_REST_Request $request
    ) {
        $helper = $this->getProjectHelper();
        $confirmMessages = [];

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['formValues'])){
            throw new Exception('No form values provided');
        }

        $navigatingAway = isset($params['navigatingAway']) && $params['navigatingAway'] ? true : false;
        $converter = $this->createAppObject(ProjectConverter::class);
        $formValues = $converter->convert($params['formValues']);

        $modelInstance = $this->loadProject($formValues);
        $this->setRegistryModelInstance($modelInstance);

        $validateResult = $this->validateValues($modelInstance, $formValues);

        if($validateResult['hasErrors']){
            return $validateResult;
        }
        
        if($formValues['status'] == ProjectDefinition::STATUS_COMPLETE
            || $formValues['status'] == ProjectDefinition::STATUS_CANCELLED){

            $openTasks = $this->getTaskHelper()->getTaskCollectionForEntity(
                $modelInstance->getId(), 
                TaskDefinition::TASK_TYPE_PROJECT,
                TaskDefinition::STATUS_OPEN
            );
    
            if(count($openTasks->getItems())){
                $confirmMessages[] = sprintf(
                    'This will close %s open task%s for the project.',
                    count($openTasks->getItems()),
                    count($openTasks->getItems()) > 1 ? 's' : ''
                );
            }
        }

        if($modelInstance->isComplete() == false 
            && $formValues['status'] == ProjectDefinition::STATUS_COMPLETE){
            
            $confirmMessages[] = 'This will complete and archive the project.';
        }

        if($confirmMessages){
            $confirmMessages[] = "Would you like to proceed?";
            $validateResult['confirm'] = implode("\r", $confirmMessages);

            return $validateResult;
        }

        return array_merge($validateResult, $this->saveProject($modelInstance, $formValues, $navigatingAway));
    }

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @return array|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function saveAction(
        $params,
        \WP_REST_Request $request
    ) {
        $helper = $this->getProjectHelper();

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['formValues'])){
            throw new Exception('No form values provided');
        }

        $navigatingAway = isset($params['navigatingAway']) && $params['navigatingAway'] ? true : false;
        $converter = $this->createAppObject(ProjectConverter::class);
        $formValues = $converter->convert($params['formValues']);
        $modelInstance = $this->loadProject($formValues);
        $this->setRegistryModelInstance($modelInstance);

        $validateResult = $this->validateValues($modelInstance, $formValues);

        if($validateResult['hasErrors']){
            return $validateResult;
        }

        $saveResult = $this->saveProject($modelInstance, $formValues, $navigatingAway);

        return $saveResult;
    }

    protected function validateValues(
        \SuttonBaker\Impresario\Model\Db\Project $modelInstance,
        $formValues
    ) {
        $blockManager = $this->getApp()->getBlockManager();
        $helper = $this->getProjectHelper();
        $saveResult = [];

        /** @var QuoteConfigurator $configurator */
        $configurator = $this->createAppObject(ProjectConfigurator::class);
        /** @var Validator $validator */
        $validator = $this->createAppObject(Validator::class)->setValues($formValues);
        $validator->configurate($configurator)->validate();

        $errorBlock = $blockManager->createBlock(Main::class, 'project.edit.form.errors');
        $errorBlock->addErrors($validator->getErrors())->setIsReplacerBlock(true);

        $this->addReplacerBlock($errorBlock);

        return [
            'hasErrors' => $validator->hasErrors(),
            'errorFields' => $validator->getErrorFields()
        ];
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Project $modelInstance
     * @param $formValues
     * @return array
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function saveProject(
        \SuttonBaker\Impresario\Model\Db\Project $modelInstance,
        $formValues,
        $navigatingAway = false
    ) {
        $saveValues = $this->getProjectHelper()->saveProject($modelInstance, $formValues);

        if($saveValues['new_save'] == true){
            $this->getApp()->getGeneralSession()->addMessage(
                "The project has been created",
                Messages::SUCCESS
            );
        }

        // No need to redirect for updating
        if($saveValues['new_save'] == false && !$saveValues['project_newly_completed']){
            $message = 'The project has been updated';
            if($navigatingAway) {
                $this->getApp()->getGeneralSession()->addMessage(
                    $message,
                    Messages::SUCCESS
                );
            } else {
                $this->addReplacerBlock(
                    $this->getModalHelper()->createAutoOpenModal(
                        'Success',
                        $message
                    )
                );
            }
        }

        if($saveValues['project_newly_completed']){
            $this->getApp()->getGeneralSession()->addMessage(
                'The project has been saved and moved to the archive',
                Messages::SUCCESS
            );

            $saveValues['redirect'] = $this->getUrlHelper()->getPageUrl(Page::PROJECT_LIST);
        }

        return $saveValues;
    }

    /**
     * @param $params
     * @return \SuttonBaker\Impresario\Model\Db\Project
     * @throws Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function loadProject($params)
    {
        $modelInstance = $this->getProjectHelper()->getProject();

        if(isset($params['project_id']) && $params['project_id']){
            $modelInstance->load($params['project_id']);

            if(!$modelInstance->getId()){
                throw new Exception('The project could not be found');
            }
        }

        return $modelInstance;
    }


    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function updatetableAction($params, \WP_REST_Request $request)
    {
        $blockManager = $this->getApp()->getBlockManager();

        /** @var StatusLink $tableBlock */
        $tableBlock = $blockManager->getBlock("{$this->blockPrefix}.list.table");

        if(isset($params['order']['dir']) && isset($params['order']['column'])){
            $tableBlock->setColumnOrder($params['order']['column'], $params['order']['dir']);
        }

        /** @var Paginator $paginatorBlock */
        $paginatorBlock = $blockManager->getBlock("{$this->blockPrefix}.list.paginator");

        if(isset($params['pageNumber'])){
            $paginatorBlock->setPage($params['pageNumber']);
        }

        $this->addReplacerBlock([$tableBlock, $paginatorBlock]);
    }

    /**
     * @param $params
     * @param \WP_REST_Request $request
     * @return bool|\WP_Error
     * @throws Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function deleteAction($params, \WP_REST_Request $request)
    {
        $helper = $this->getProjectHelper();

        if(!$helper->currentUserCanEdit()) {
            return $this->getAccessDeniedError();
        }

        if(!isset($params['id'])){
            throw new Exception('The item could not be found');
        }

        /** @var \SuttonBaker\Impresario\Model\Db\Project $item */
        $item = $this->createAppObject(
            \SuttonBaker\Impresario\Definition\Project::DEFINITION_MODEL
        )->load($params['id']);

        if($item->isComplete()){
            throw new Exception('The project cannot be deleted because it has been completed');
        }

        if(!$item->getId()){
            throw new Exception('The project could not be found');
        }

        $helper->deleteProject($item);
        $this->addMessage('The project has been removed', Messages::SUCCESS);

        return true;
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getProjectHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Project');
    }

    /**
     * @param $modelInstance
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function setRegistryModelInstance($modelInstance)
    {
        $this->getApp()->getRegistry()->register('model_instance', $modelInstance);
        return $this;
    }
}