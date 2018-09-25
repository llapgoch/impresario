<?php

namespace SuttonBaker\Impresario\Controller\Quote;

use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
use SuttonBaker\Impresario\Definition\Roles;

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

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_QUOTE,
        Roles::CAP_VIEW_QUOTE,
        Roles::CAP_ALL
    ];

    /** @var \DaveBaker\Form\Block\Form $editForm */
    protected $editForm;
    /** @var \SuttonBaker\Impresario\Model\Db\Quote */
    protected $parentItem;
    /** @var \SuttonBaker\Impresario\Model\Db\Enquiry */
    protected $enquiryItem;
    /** @var \SuttonBaker\Impresario\Model\Db\Quote */
    protected $modelInstance;


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
                $this->getUrlHelper()->getPageUrl(Page::QUOTE_LIST)
            );
        }

        $this->modelInstance = $this->getQuoteHelper()->getQuote($instanceId);
        $this->getApp()->getRegistry()->register('model_instance', $this->modelInstance);

        if(!$this->modelInstance->getId()){
            $this->addMessage('The quote could not be found');

            $this->redirectToPage(Page::QUOTE_LIST);
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
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function execute()
    {
        if(!($this->editForm = $this->getApp()->getBlockManager()->getBlock('quote.form.edit'))){
            return;
        }

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        wp_register_script('impresario_calculator', get_template_directory_uri() . '/assets/js/profit-calculator.js', ['jquery']);
        wp_enqueue_script('impresario_calculator');


        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');
        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        $data = $this->modelInstance->getData();
        // Apply the values to the form element
        if($this->modelInstance->getId()) {

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
}