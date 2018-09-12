<?php

namespace SuttonBaker\Impresario\Controller\Variation;

use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;
use \SuttonBaker\Impresario\Definition\Variation as VariationDefinition;
use \SuttonBaker\Impresario\Definition\Page as PageDefinition;


/**
 * Class EditController
 * @package SuttonBaker\Impresario\Controller\Variation
 */
class EditController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const VARIATION_TYPE_PARAM = 'variation_type';
    const PROJECT_ID_PARAM = 'project_id';
    const ENTITY_ID_PARAM = 'variation_id';

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_VARIATION,
        Roles::CAP_VIEW_VARIATION,
        Roles::CAP_ALL
    ];

    /** @var \DaveBaker\Form\Block\Form $editForm */
    protected $editForm;
    /** @var \SuttonBaker\Impresario\Model\Db\Variation */
    protected $modelInstance;
    /** @var \SuttonBaker\Impresario\Model\Db\Project */
    protected $project;

    protected $nonUserValues = [
        'variation_id',
        'created_by_id',
        'last_edited_by_id',
        'profit',
        'gp',
        'created_at',
        'updated_at',
        'is_deleted'
    ];

    /**
     * @return bool|\SuttonBaker\Impresario\Controller\Base
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _preDispatch()
    {

        $projectId = $this->getRequest()->getParam(self::PROJECT_ID_PARAM);
        $instanceId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM);
        $this->project = $this->getProjectHelper()->getProject();

        $this->setModelInstance($this->getVariationHelper()->getVariation());

        if(!$instanceId && !$projectId){
            $this->addMessage('Variations must be derived from a project');
            return $this->getResponse()->redirectReferer();
        }

        if($instanceId){
            // We're loading, fellas!
            $this->modelInstance->load($instanceId);
            $this->project->load($this->modelInstance->getProjectId());

            if(!$this->modelInstance->getId() || $this->modelInstance->getIsDeleted()){
                $this->addMessage('The variation could not be found');
                return $this->getResponse()->redirectReferer();
            }

        }else {
            $this->project->load($projectId);

            if(!$this->project->getId() || $this->project->getIsDeleted()){
                $this->addMessage('The project could not be found');
                $this->getResponse()->redirectReferer();
            }
        }

        $this->getApp()->getRegistry()->register('project', $this->project);
        $this->getApp()->getRegistry()->register('model_instance', $this->modelInstance);
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
        if(!($this->editForm = $this->getApp()->getBlockManager()->getBlock('variation.form.edit'))){
            return;
        }

        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        wp_register_script('impresario_calculator', get_template_directory_uri() . '/assets/js/profit-calculator.js', ['jquery']);
        wp_enqueue_script('impresario_calculator');

        // Form submission
        if($this->getRequest()->getPostParam('action')){
            $postParams = $this->getRequest()->getPostParams();

            // Convert dates to DB
            if (isset($postParams['date_approved'])){
                $postParams['date_approved'] = $helper->localDateToDb($postParams['date_approved']);
            }

            /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
            $configurator = $this->createAppObject('\SuttonBaker\Impresario\Form\VariationConfigurator');

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

        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Apply the values to the form element
        if($this->modelInstance->getId()) {
            $data = $this->modelInstance->getData();

            if($this->modelInstance->getDateApproved()){
                $data['date_approved'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateApproved());
            }

            if($this->modelInstance->getNetCost()){
                $data['net_cost'] = (float)$this->modelInstance->getNetCost();
            }

            if($this->modelInstance->getValue()){
                $data['value'] = (float)$this->modelInstance->getValue();
            }

            $applicator->configure(
                $this->editForm,
                $data
            );
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
        if(!$this->modelInstance->getVariationId()) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
            $data['project_id'] = $this->project->getId();
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        $this->modelInstance->setData($data)->save();

        $this->addMessage(
            "The variation has been " . ($this->modelInstance->getId() ? 'updated' : 'created'),
            Messages::SUCCESS
        );

        return $this;
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
            'variation.edit.form.errors'
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