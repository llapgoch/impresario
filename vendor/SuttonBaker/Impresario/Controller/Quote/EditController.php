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

    /** @var \DaveBaker\Form\Block\Form $editForm */
    protected $editForm;
    protected $parentItem;


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
        $instanceId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM);
        $parentItem = null;

        $modelInstance = $this->getQuoteHelper();

        if($instanceId){
            // We're loading, fellas!
            $modelInstance->load($instanceId);

            if(!$modelInstance->getId() || $modelInstance->getIsDeleted()){
                $this->addMessage('The quote could not be found', Messages::ERROR);
                return $this->getResponse()->redirectReferer();
            }

        }

        if( $parentId = $this->getRequest()->getParam(self::PARENT_ID_PARAM)) {
            $this->getQuoteHelper()->getQuote($parentItem);
            $this->parentItem = $parentItem;

            if(!$parentItem->getId()){
                $this->addMessage('The parent quote could not be found');
                return $this->getResponse()->redirectReferer();
            }
        }


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

            // Don't save a completed user if status isn't completed
            if (isset($postParams['status'])) {
                if ($postParams['status'] !== QuoteDefinition::STATUS_COMPLETE) {
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

            if(isset($postParams['date_received'])){
                $postParams['date_received'] = $helper->localDateToDb($postParams['date_received']);
            }

            if(isset($postParams['date_required'])){
                $postParams['date_required'] = $helper->localDateToDb($postParams['date_required']);
            }

            if(isset($postParams['date_return_by'])){
                $postParams['date_return_by'] = $helper->localDateToDb($postParams['date_return_by']);
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
            $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::QUOTE_LIST);
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

            if($modelInstance->getDateReceived()){
                $data['date_received'] = $helper->utcDbDateToShortLocalOutput($modelInstance->getDateReceived());
            }

            if($modelInstance->getDateRequired()){
                $data['date_required'] = $helper->utcDbDateToShortLocalOutput($modelInstance->getDateRequired());
            }

            if($modelInstance->getDateReturnBy()){
                $data['date_return_by'] = $helper->utcDbDateToShortLocalOutput($modelInstance->getDateReturnBy());
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

        // Add created by user
        if(!$data['quote_id']) {
            $data['created_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();
        }

        $data['last_edited_by_id'] = $this->getApp()->getHelper('User')->getCurrentUserId();


        /** @var \SuttonBaker\Impresario\Model\Db\Quote $modelInstance */
        $modelInstance = $this->createAppObject(QuoteDefinition::DEFINITION_MODEL);
        $modelInstance->setData($data)->save();

        $this->addMessage("The quote has been " . ($data['quote_id'] ? 'updated' : 'added'));
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