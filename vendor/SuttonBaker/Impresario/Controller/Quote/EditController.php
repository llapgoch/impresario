<?php

namespace SuttonBaker\Impresario\Controller\Quote;

use DaveBaker\Core\Definitions\Messages;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;

/**
 * Class EditController
 * @package SuttonBaker\Impresario\Controller\Quote
 */
class EditController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const PARENT_ID_PARAM = 'parent_id';
    const ENTITY_ID_PARAM = 'quote_id';
    const ENQUIRY_ID_PARAM = 'enquiry_id';

    /** @var \DaveBaker\Form\Block\Form $editForm */
    protected $editForm;
    /** @var \SuttonBaker\Impresario\Model\Db\Quote */
    protected $parentItem;
    /** @var \SuttonBaker\Impresario\Model\Db\Enquiry */
    protected $enquiryItem;
    /** @var \SuttonBaker\Impresario\Model\Db\Quote */
    protected $modelInstance;

    /** @var array  */
    protected $nonUserValues = [
        'quote_id',
        'created_by_id',
        'last_edited_by_id',
        'client_id',
        'enquiry_id',
        'parent_id',
        'created_at',
        'updated_at',
        'is_deleted'
    ];


    /**
     * @return \DaveBaker\Core\App\Response|object|\SuttonBaker\Impresario\Controller\Base|null
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     */
    protected function _preDispatch()
    {
        $instanceId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM);
        $this->parentItem = $this->getQuoteHelper()->getQuote();
        $this->enquiryItem = $this->getEnquiryHelper()->getEnquiry();
        $this->modelInstance = $this->getQuoteHelper()->getQuote();

        $this->getApp()->getRegistry()->register('enquiry_item', $this->enquiryItem);


        if($instanceId){
            // We're loading, fellas!
            $this->modelInstance->load($instanceId);

            if(!$this->modelInstance->getId() || $this->modelInstance->getIsDeleted()){
                $this->addMessage('The quote could not be found', Messages::ERROR);
                return $this->getResponse()->redirectReferer();
            }

        }

        if($enquiryId = $this->getRequest()->getParam(self::ENQUIRY_ID_PARAM)) {

            $this->enquiryItem->load($enquiryId);

            if(!$this->enquiryItem->getId()){
                $this->addMessage('The enquiry could not be found');
                return $this->getResponse()->redirectReferer();
            }

            if($this->enquiryItem->getQuoteEntity()->getId()){
                $this->addMessage('A quote already exists for this enquiry item');
                return $this->getResponse()->redirectToPage(
                    \SuttonBaker\Impresario\Definition\Page::QUOTE_LIST
                );
            }
        } else {
            if ($enquiryId = $this->modelInstance->getEnquiryId()) {
                $this->enquiryItem->load($enquiryId);

                if(!$this->enquiryItem->getId()){
                    $this->addMessage('The enquiry could not be found');
                    return $this->getResponse()->redirectReferer();
                }

            }
        }

        if(!$this->enquiryItem->getId() && !$this->parentItem->getId()){
            $this->addMessage('A quote must be derived from an enquiry or another quote');
            return $this->getResponse()->redirectReferer();
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
        if(!($this->editForm = $this->getApp()->getBlockManager()->getBlock('quote.form.edit'))){
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
            if (isset($postParams['date_received'])){
                $postParams['date_received'] = $helper->localDateToDb($postParams['date_received']);
            }


            if(isset($postParams['date_required'])){
                $postParams['date_required'] = $helper->localDateToDb($postParams['date_required']);
            }

            if(isset($postParams['date_return_by'])){
                $postParams['date_return_by'] = $helper->localDateToDb($postParams['date_return_by']);
            }

            if(isset($postParams['date_returned'])){
                $postParams['date_returned'] = $helper->localDateToDb($postParams['date_returned']);
            }

            if(isset($postParams['date_completed'])){
                $postParams['date_completed'] = $helper->localDateToDb($postParams['date_completed']);
            }


            /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
            $configurator = $this->createAppObject('\SuttonBaker\Impresario\Form\QuoteConfigurator');

            /** @var \DaveBaker\Form\Validation\Validator $validator */
            $validator = $this->createAppObject('\DaveBaker\Form\Validation\Validator')
                ->setValues($postParams)
                ->configurate($configurator);

            if(!$validator->validate()){
                return $this->prepareFormErrors($validator);
            }

            $this->saveFormValues($postParams);

            if(!$this->getApp()->getResponse()->redirectToReturnUrl()) {
                $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::QUOTE_LIST);
            }
        }



        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Apply the values to the form element
        if($this->modelInstance->getId()) {
            $data = $this->modelInstance->getData();

            if($this->modelInstance->getDateReturned()){
                $data['date_returned'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateReturned());
            }

            if($this->modelInstance->getTargetDate()){
                $data['target_date'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getTargetDate());
            }

            if($this->modelInstance->getDateCompleted()){
                $data['date_completed'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateCompleted());
            }

            if($this->modelInstance->getDateReceived()){
                $data['date_received'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateReceived());
            }

            if($this->modelInstance->getDateRequired()){
                $data['date_required'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateRequired());
            }

            if($this->modelInstance->getDateReturnBy()){
                $data['date_return_by'] = $helper->utcDbDateToShortLocalOutput($this->modelInstance->getDateReturnBy());
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

    /**
     * @param $data
     * @return $this|void
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
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
        if(!$this->modelInstance->getQuoteId()) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();

        if(!$this->modelInstance->getClientId()) {
            $data['client_id'] = $this->enquiryItem->getClientId();
        }

        if(!$this->modelInstance->getEnquiryId()){
            $data['enquiry_id'] = $this->enquiryItem->getEnquiryId();
        }

        if(!$this->modelInstance->getParentId() && $this->parentItem->getId()){
            $data['parent_id'] = $this->parentItem->getId();
        }

        // Check if we need to create a new quote
        if($this->modelInstance->getId() && ((float) $this->modelInstance->getNetCost() !== (float) $data['net_cost'] ||
            (float) $this->modelInstance->getNetSell() !== (float) $data['net_sell'])
        ) {
            $this->modelInstance = $this->getQuoteHelper()->duplicateQuote($this->modelInstance);
        }


        $this->addMessage("The quote has been " . ($this->modelInstance->getId() ? 'updated' : 'added'));
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
            'quote.edit.form.errors'
        )->setOrder('after', 'quote.form.edit.heading')->addErrors($validator->getErrors());

        $this->editForm->addChildBlock($errorBlock);

        // Sets the values back onto the form element
        $applicator->configure(
            $this->editForm,
            $this->getRequest()->getPostParams()
        );
    }
}