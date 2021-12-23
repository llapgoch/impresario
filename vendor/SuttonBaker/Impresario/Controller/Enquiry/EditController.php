<?php

namespace SuttonBaker\Impresario\Controller\Enquiry;

use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;

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
    protected $capabilities = [
        Roles::CAP_EDIT_ENQUIRY,
        Roles::CAP_VIEW_ENQUIRY,
        Roles::CAP_ALL
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
            if(!$this->modelInstance->getId()){
                $this->addMessage('The enquiry does not exist', Messages::ERROR);
                $this->redirectToPage(\SuttonBaker\Impresario\Definition\Page::ENQUIRY_LIST);
            }
        }
    }

    /**
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
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
}