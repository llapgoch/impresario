<?php

namespace SuttonBaker\Impresario\Controller\Project;

use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class EditController
 * @package SuttonBaker\Impresario\Controller\Project
 */
class EditController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const ENTITY_ID_PARAM = 'project_id';
    /** @var \DaveBaker\Form\Block\Form $editForm */
    protected $editForm;
    /** @var \SuttonBaker\Impresario\Model\Db\Project */
    protected $modelInstance;
    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_PROJECT,
        Roles::CAP_VIEW_PROJECT,
        Roles::CAP_ALL
    ];

    /** @var array  */
    protected $nonUserValues = [
        'project_id',
        'client_id',
        'client_requested_by',
        'client_reference',
        'project_name',
        'created_by_id',
        'last_edited_by_id',
        'client_id',
        'quote_id',
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
    protected function _preDispatch()
    {

        if(!($instanceId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM))){
            return $this->getResponse()->redirectReferer(
                $this->getUrlHelper()->getPageUrl(Page::PROJECT_LIST)
            );
        }

        $this->modelInstance = $this->getProjectHelper()->getProject($instanceId);

        if(!$this->modelInstance->getId()){
            $this->addMessage('The project could not be found');

            return $this->getResponse()->redirectReferer(
                $this->getUrlHelper()->getPageUrl(Page::PROJECT_LIST)
            );
        }
    }

    /**
     * @return \DaveBaker\Core\App\Response|object
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function execute()
    {
        if(!($this->editForm = $this->getApp()->getBlockManager()->getBlock('project.form.edit'))){
            return;
        }

        wp_register_script('impresario_project', get_template_directory_uri() . '/assets/js/profit-calculator.js', ['jquery']);
        wp_enqueue_script('impresario_project');

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');


        // Form submission
        if($this->getRequest()->getPostParam('action')){
            $postParams = $this->modifyFormValuesForSave($this->getRequest()->getPostParams());

            /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
            $configurator = $this->createAppObject('\SuttonBaker\Impresario\Form\ProjectConfigurator');

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
        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        // Apply the values to the form element
        if($this->modelInstance->getId()) {
            $data = $this->modelInstance->getData();

            if($this->modelInstance->getDateReceived()){
                $data['date_received'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateReceived());
            }

            if($this->modelInstance->getDateRequired()){
                $data['date_required'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateRequired());
            }

            if($this->modelInstance->getProjectStartDate()){
                $data['project_start_date'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getProjectStartDate());
            }

            if($this->modelInstance->getProjectEndDate()){
                $data['project_end_date'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getProjectEndDate());
            }


            if($this->modelInstance->getNetSell()){
                $data['net_sell'] = (float) $this->modelInstance->getNetSell();
            }

            if($this->modelInstance->getNetSell()){
                $data['net_cost'] = (float) $this->modelInstance->getNetCost();
            }

            $applicator->configure(
                $this->editForm,
                $data
            );
        }
    }

    protected function modifyFormValuesForSave($postParams)
    {
        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        // Convert dates to DB
        if (isset($postParams['date_received'])){
            $postParams['date_received'] = $helper->localDateToDb($postParams['date_received']);
        }

        if(isset($postParams['date_required'])){
            $postParams['date_required'] = $helper->localDateToDb($postParams['date_required']);
        }

        if(isset($postParams['project_start_date'])){
            $postParams['project_start_date'] = $helper->localDateToDb($postParams['project_start_date']);
        }

        if(isset($postParams['project_end_date'])){
            $postParams['project_end_date'] = $helper->localDateToDb($postParams['project_end_date']);
        }

        return $postParams;
    }

    /**
     * @param $data
     * @return $this
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function saveFormValues($data)
    {
        if(!$this->getApp()->getHelper('User')->isLoggedIn()){
            return;
        }

        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        foreach($this->nonUserValues as $nonUserValue){
            if(isset($data[$nonUserValue])){
                unset($data[$nonUserValue]);
            }
        }


        // Add created by user
        if(!$this->modelInstance->getId()) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        $this->addMessage(
            "The project has been " . ($this->modelInstance->getId() ? 'updated' : 'created'),
            Messages::SUCCESS
        );


        $this->modelInstance->setData($data)->save();
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
            'project.edit.form.errors'
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