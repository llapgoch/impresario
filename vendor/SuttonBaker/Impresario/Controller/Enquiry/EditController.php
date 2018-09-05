<?php

namespace SuttonBaker\Impresario\Controller\Enquiry;

use DaveBaker\Core\Definitions\Messages;
use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;

/**
 * Class EnquiryEditController
 * @package SuttonBaker\Impresario\Controller\Enquiry
 */
class EditController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    /** @var \DaveBaker\Form\Block\Form $enquiryEditForm */
    protected $enquiryEditForm;
    /** @var \SuttonBaker\Impresario\Model\Db\Enquiry */
    protected $modelInstance;
    /** @var bool  */
    protected $editMode = false;


    /** @var array  */
    protected $nonUserValues = [
        'enquiry_id',
        'created_by_id',
        'created_at',
        'updated_at',
        'is_deleted',
        'last_edited_by_id'
    ];

    /**
     * @return \SuttonBaker\Impresario\Controller\Base|void
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $this->modelInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Enquiry');

        $this->getApp()->getRegistry()->register('model_instance', $this->modelInstance);

        if($instanceId = (int) $this->getRequest()->getParam('enquiry_id')) {
            // We're loading, fellas!
            $this->modelInstance->load($instanceId);
            $this->editMode = true;
        }

        if($instanceId = (int) $this->getRequest()->getParam('enquiry_id')){
            if(!$this->modelInstance->getId() || $this->modelInstance->getIsDeleted()){
                $this->addMessage('The enquiry does not exist', Messages::ERROR);
                $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::ENQUIRY_LIST);
            }
        }
    }

    /**
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
        if(!($this->enquiryEditForm = $this->getApp()->getBlockManager()->getBlock('enquiry.form.edit'))){
            return;
        }

        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        wp_register_script('impresario_enquiry_edit', get_template_directory_uri() . '/assets/js/enquiry-edit.js', ['jquery']);
        wp_enqueue_script('impresario_enquiry_edit');


        // Form submission
        if($this->getRequest()->getPostParam('action')){
            $postParams = $this->getRequest()->getPostParams();

            // Convert dates to DB
            if (isset($postParams['date_received'])){
                $postParams['date_received'] = $helper->localDateToDb($postParams['date_received']);
            }

            if(isset($postParams['target_date'])){
                $postParams['target_date'] = $helper->localDateToDb($postParams['target_date']);
            }

            if(isset($postParams['date_completed'])){
                $postParams['date_completed'] = $helper->localDateToDb($postParams['date_completed']);
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
        }


        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Apply the values to the form element
        if($this->modelInstance->getId()) {
            $data = $this->modelInstance->getData();

            if($this->modelInstance->getDateReceived()){
                $data['date_received'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateReceived());
            }

            if($this->modelInstance->getDateCompleted()){
                $data['date_completed'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateCompleted());
            }

            if($this->modelInstance->getTargetDate()){
                $data['target_date'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getTargetDate());
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
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     */
    protected function saveFormValues($data)
    {
        if(!$this->getApp()->getHelper('User')->isLoggedIn()){
            return $this;
        }

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

        $this->modelInstance->setData($data)->save();

        // Create a quote if enquiry is complete
        if($data['status'] == EnquiryDefinition::STATUS_COMPLETE){
            $quote = $this->getQuoteHelper()->getNewestQuoteForEnquiry($this->modelInstance->getId());

            if(!$quote->getId()) {
                $quote = $this->getQuoteHelper()->createQuoteFromEnquiry($this->modelInstance->getId());

                $this->addMessage('A new quote has been created for the enquiry', Messages::SUCCESS);

                $this->redirectToPage(
                    PageDefinition::QUOTE_EDIT,
                    ['quote_id' => $quote->getId()]
                );
            }
        }

        $this->addMessage(
            "The enquiry has been " . ($this->editMode ? 'updated' : 'created'),
            Messages::SUCCESS
        );

        if(!$this->getApp()->getResponse()->redirectToReturnUrl()) {
            return $this->redirectToPage(PageDefinition::ENQUIRY_LIST);
        }

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
        )->setOrder('before', '')->addErrors($validator->getErrors());

        $this->enquiryEditForm->addChildBlock($errorBlock);

        // Sets the values back onto the form element
        $applicator->configure(
            $this->enquiryEditForm,
            $this->getRequest()->getPostParams(),
            $validator
        );
    }
}