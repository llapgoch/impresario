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
        $this->getApp()->getRegistry()->register('model_instance', $this->modelInstance);

        if($this->getRequest()->getPostParam('action')) {
            if ($this->modelInstance->isComplete()) {
                $this->addMessage('The project has been archived and cannot be updated');

                return $this->getResponse()->redirectReferer(
                    $this->getUrlHelper()->getPageUrl(Page::PROJECT_LIST)
                );
            }
        }


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

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');


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

            $data['total_net_sell'] = $this->getLocaleHelper()->formatCurrency($this->modelInstance->getTotalNetSell());
            $data['total_net_cost'] = $this->getLocaleHelper()->formatCurrency($this->modelInstance->getTotalNetCost());
            $data['amount_invoiced'] = $this->getLocaleHelper()->formatCurrency($this->modelInstance->getAmountInvoiced());
            $data['invoice_amount_remaining'] = $this->getLocaleHelper()->formatCurrency(
                $this->modelInstance->getInvoiceAmountRemaining()
            );

            if($this->modelInstance->getProfit()){
                $data['profit'] = $this->getLocaleHelper()->formatCurrency($this->modelInstance->getProfit());
            }

            $applicator->configure(
                $this->editForm,
                $data
            );
        }
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