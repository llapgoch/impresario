<?php

namespace SuttonBaker\Impresario\Block\Enquiry\Form;
/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \DaveBaker\Form\Block\Form
{
    const ID_KEY = 'enquiry_id';
    const PREFIX_KEY = 'enquiry';
    const PREFIX_NAME = 'Enquiry';
    /**
     * @return \DaveBaker\Form\Block\Form|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $heading = "Add {$prefixName}";
        $editMode = false;

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Enquiry')->load($entityId);
            $heading = "Edit {$prefixName}";
            $editMode = true;
        }

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Core\Block\Html\Heading', "{$prefixKey}.form.edit.heading")
                ->setHeading($heading)
        );

        // Date Received
        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Label', "{$prefixKey}.form.edit.date.received.label")
                ->setLabelName("Date Received")
                ->setForId('edit_form_date_received')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', "{$prefixKey}.form.edit.date.received.input")
                ->setElementName("{$prefixKey}_date_received")
                ->addAttribute(['id' => 'edit_form_date_received'])
                ->addClass('js-date-picker')
        );


    }
}