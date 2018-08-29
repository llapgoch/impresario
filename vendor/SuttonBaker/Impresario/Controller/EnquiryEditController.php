<?php

namespace SuttonBaker\Impresario\Controller;

use DaveBaker\Core\Definitions\Messages;

/**
 * Class EnquiryEditController
 * @package SuttonBaker\Impresario\Controller
 */
class EnquiryEditController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    /** @var \DaveBaker\Form\Block\Form $enquiryEditForm */
    protected $enquiryEditForm;

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
        if(!($this->enquiryEditForm = $this->getApp()->getBlockManager()->getBlock('enquiry.form.edit'))){
            return;
        }

        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        $modelInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Enquiry');

        // Form submission
        if($this->getRequest()->getPostParam('action')){
            $postParams = $this->getRequest()->getPostParams();

            // Don't save a completed user if status isn't completed
            if (isset($postParams['status'])) {
                if ($postParams['status'] !== \SuttonBaker\Impresario\Definition\Enquiry::STATUS_COMPLETE) {
                    $postParams['completed_by_id'] = null;
                }
            }

            // Convert dates to DB
            if (isset($postParams['date_received'])){
                $postParams['date_received'] = $helper->localDateToDb($postParams['date_received']);
            }

            if(isset($postParams['target_date'])){
                $postParams['target_date'] = $helper->localDateToDb($postParams['target_date']);
            }

            /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
            $configurator = $this->createAppObject('\SuttonBaker\Impresario\Form\EnquiryConfigurator');

            /** @var \DaveBaker\Form\Validation\Validator $validator */
            $validator = $this->createAppObject('\DaveBaker\Form\Validation\Validator')
                ->setValues($postParams)
                ->configurate($configurator);

            if(!$validator->validate()){
                return $this->prepareFormErrors($validator);
            }

            $this->saveFormValues($postParams);
            $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::ENQUIRY_LIST);
        }

        if($instanceId = (int) $this->getRequest()->getParam('enquiry_id')){
            // We're loading, fellas!
            $modelInstance->load($instanceId);

            if(!$modelInstance->getId() || $modelInstance->getIsDeleted()){
                $this->addMessage('The enquiry does not exist', Messages::ERROR);
                $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::ENQUIRY_LIST);
            }
        }

        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Apply the values to the form element
        if($modelInstance->getId()) {
            $data = $modelInstance->getData();

            if($modelInstance->getDateReceived()){
                $data['date_received'] = $helper->utcDbDateToShortLocalOutput($modelInstance->getDateReceived());
            }

            if($modelInstance->getTargetDate()){
                $data['target_date'] = $helper->utcDbDateToShortLocalOutput($modelInstance->getTargetDate());
            }

            $applicator->configure(
                $this->enquiryEditForm,
                $data
            );
        }
    }

    /**
     * @param $data
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function saveFormValues($data)
    {
        if(!$this->getApp()->getHelper('User')->isLoggedIn()){
            return $this;
        }

        // Add created by user
        if(!$data['enquiry_id']) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        /** @var \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry */
        $enquiry = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Enquiry');
        $this->addMessage("The enquiry has been " . ($data['enquiry_id'] ? 'updated' : 'added'));
        $enquiry->setData($data)->save();
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
            'enquiry.edit.form.errors'
        )->setOrder('after', 'enquiry.form.edit.heading')->addErrors($validator->getErrors());

        $this->enquiryEditForm->addChildBlock($errorBlock);

        // Sets the values back onto the form element
        $applicator->configure(
            $this->enquiryEditForm,
            $this->getRequest()->getPostParams()
        );
    }
}