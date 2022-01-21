<?php

namespace SuttonBaker\Impresario\Controller\Cost;

use DaveBaker\Core\Definitions\Messages;
use DaveBaker\Core\Definitions\Upload;
use \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Helper\Cost;


/**
 * Class EditController
 * @package SuttonBaker\Impresario\Controller\Cost
 */
class EditController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const COST_TYPE_PARAM = 'cost_type';
    const PARENT_ID_PARAM = 'parent_id';
    const ENTITY_ID_PARAM = 'cost_id';

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_COST,
        Roles::CAP_VIEW_COST,
        Roles::CAP_ALL
    ];

    /** @var \DaveBaker\Form\Block\Form $editForm */
    protected $editForm;
    protected $parentItem;
    protected $costType;
    protected $modelInstance;

    protected $nonUserValues = [
        'cost_id',
        'created_by_id',
        'last_edited_by_id',
        'cost_type',
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
        $costType = $this->getRequest()->getParam(self::COST_TYPE_PARAM);
        $parentId = $this->getRequest()->getParam(self::PARENT_ID_PARAM);
        $instanceId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM);
        
        $this->setModelInstance($this->getCostHelper()->getCost());

        if(!$instanceId){
            $this->modelInstance->setTaskType($this->costType);
            $this->modelInstance->setParentId($parentId);
        }



        if($instanceId){
            // We're loading, fellas!
            $this->modelInstance->load($instanceId);

            if(!$this->modelInstance->getId()){
                $this->addMessage('The cost could not be found', Messages::ERROR);
                return $this->getResponse()->redirectReferer();
            }

        }else {
            if (!$this->getCostHelper()->isValidCostType($costType)) {
                $this->addMessage('Invalid Cost Type');
                $this->getResponse()->redirectReferer();
            }
        }

        $this->setParentItem($this->getParentItem($this->modelInstance));
        $this->setCostType($this->getCostHelper()->getCostTypeForParent($this->parentItem));

        if(!$this->parentItem || !$this->parentItem->getId()){
            $this->addMessage('The parent item of the cost could not be found');
            return $this->getResponse()->redirectReferer();
        }

        if(!$this->costType){
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
        if(!($this->editForm = $this->getApp()->getBlockManager()->getBlock('cost.form.edit'))){
            return;
        }


        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Apply the values to the form element
        if($this->modelInstance->getId()) {
            $data = $this->modelInstance->getData();

            if($this->modelInstance->getCostDate()){
                $data['cost_date'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getCostDate());
            }

            if($this->modelInstance->getValue()){
                $data['value'] = (float) $this->modelInstance->getValue();
            }

            $applicator->configure(
                $this->editForm,
                $data
            );
        }

        // Form submission
        if($this->getRequest()->getPostParam('action')){
            $postParams = $this->getRequest()->getPostParams();

            // Convert dates to DB
            if (isset($postParams['cost_date'])){
                $postParams['cost_date'] = $helper->localDateToDb($postParams['cost_date']);
            }

            /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
            $configurator = $this->createAppObject('\SuttonBaker\Impresario\Form\CostConfigurator');

            /** @var \DaveBaker\Form\Validation\Validator $validator */
            $validator = $this->createAppObject('\DaveBaker\Form\Validation\Validator')
                ->setValues($postParams)
                ->configurate($configurator);

            if(!$validator->validate()){
                return $this->prepareFormErrors($validator);
            }

            $this->saveFormValues($postParams);

            if(!$this->getApp()->getResponse()->redirectToReturnUrl()) {
                $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::PROJECT_LIST);
            }
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
    protected function setCostType($costType)
    {
        $this->costType = $costType;
        $this->getApp()->getRegistry()->register('cost_type', $costType);
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Cost $instance
     * @return \SuttonBaker\Impresario\Model\Db\Project|\SuttonBaker\Impresario\Model\Db\Quote
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getParentItem(\SuttonBaker\Impresario\Model\Db\Cost $instance)
    {
        $parentId = null;

        if($instance->getId()){
            $costType = $instance->getCostType();
            $parentId = $instance->getParentId();
        }else{
            $costType = $this->getRequest()->getParam(self::COST_TYPE_PARAM);
            $parentId = $this->getRequest()->getParam(self::PARENT_ID_PARAM);
        }

        if($costType == CostDefinition::COST_TYPE_PROJECT){
            return $this->getProjectHelper()->getProject($parentId);
        }

        return null;
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

        $newSave = false;

        // Add created by user
        if(!$this->modelInstance->getId()) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
            $data['cost_type'] = $this->costType;
            $data['parent_id'] = $this->parentItem->getId();
            $newSave = true;
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        $this->modelInstance->setData($data)->save();

        if($newSave && ($temporaryId = $this->getRequest()->getPostParam(Upload::TEMPORARY_IDENTIFIER_ELEMENT_NAME))){
            // Assign any uploads to the enquiry
            $this->getUploadHelper()->assignTemporaryUploadsToParent(
                $temporaryId,
                \SuttonBaker\Impresario\Definition\Upload::TYPE_COST,
                $this->modelInstance->getId()
            );
        }

        $this->addMessage(
            "The cost has been " . ($newSave ? 'created' : 'updated'),
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
            'cost.edit.form.errors'
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