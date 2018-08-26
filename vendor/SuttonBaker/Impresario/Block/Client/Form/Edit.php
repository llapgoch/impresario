<?php

namespace SuttonBaker\Impresario\Block\Client\Form;
/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \DaveBaker\Form\Block\Form
{
    /**
     * @return \DaveBaker\Form\Block\Form|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $heading = 'Add Client';
        $editMode = false;

        if($clientId = $this->getRequest()->getParam('client_id')){
            $client = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Client')->load($clientId);
            $heading = "Edit '{$client->getClientName()}'";
            $editMode = true;
        }

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Core\Block\Html\Heading', 'client.form.edit.heading')
                ->setHeading($heading)
        );

        // Name

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Label', 'client.form.edit.name.label')
                ->setElementName('client.form.edit.name.label')
                ->setLabelName('Client Name')
                ->setForId('edit_form_name')
        );
        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'client.form.edit.name')
                ->setElementName('client_name')
                ->addAttribute(['id' => 'edit_form_name'])
        );

        // Address Line 1

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Label', 'client.form.address1.label')
                ->setLabelName('Address Line 1')
                ->setForId('edit_form_address_line1')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'client.form.edit.address_line1')
                ->setElementName('address_line1')
                ->addAttribute(['id' => 'edit_form_address_line1'])
        );

        // Address Line 2
        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Label', 'client.form.address2.label')
                ->setLabelName('Address Line 2')
                ->setForId('edit_form_address_line2')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'client.form.edit.address_line2')
                ->setElementName('address_line2')
                ->addAttribute(['id' => 'edit_form_address_line2'])
        );

        // Address Line 3
        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Label', 'client.form.address3.label')
                ->setLabelName('Address Line 3')
                ->setForId('edit_form_address_line3')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'client.form.edit.address_line3')
                ->setElementName('address_line3')
                ->addAttribute(['id' => 'edit_form_address_line3'])
        );

        // Postcode

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Label', 'client.form.postcode.label')
                ->setLabelName('Postcode')
                ->setForId('edit_form_postcode')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'client.form.edit.postcode')
                ->setElementName('postcode')
                ->addAttribute(['id' => 'edit_form_postcode'])
        );

        // County
        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Label', 'client.form.county.label')
                ->setLabelName('County')
                ->setForId('edit_form_county')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'client.form.edit.county')
                ->setElementName('county')
                ->addAttribute(['id' => 'edit_form_county'])
        );

        // Sales contact phone
        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Label', 'client.form.sales.contact.phone.label')
                ->setLabelName('Sales Phone Number')
                ->setForId('edit_form_sales_contact_phone')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'client.form.edit.sales.contact.phone')
                ->setElementName('sales_contact_phone')
                ->addAttribute(['id' => 'edit_form_sales_contact_phone'])
        );

        // Sales contact name
        $this->addChildBlock(
        $this->createBlock('\DaveBaker\Form\Block\Label', 'client.form.sales.contact.name.label')
            ->setLabelName('Sales Contact Name')
            ->setForId('edit_form_sales_contact_name')
         );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'client.form.edit.sales.contact.name')
                ->setElementName('sales_contact')
                ->addAttribute(['id' => 'edit_form_sales_contact_name'])
        );


        // Accounts contact phone
        $this->addChildBlock(
        $this->createBlock('\DaveBaker\Form\Block\Label', 'client.form.accounts.phone.label')
            ->setLabelName('Accounts Phone Number')
            ->setForId('edit_form_accounts_contact_phone')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'client.form.edit.sales.contact')
                ->setElementName('accounts_contact_phone')
                ->addAttribute(['id' => 'edit_form_accounts_contact_phone'])
        );

        // Accounts contact name
        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Label', 'client.form.accounts.contact.label')
                ->setLabelName('Accounts Contact Name')
                ->setForId('edit_form_accounts_contact_name')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Text', 'client.form.edit.account.contact')
                ->setElementName('accounts_contact')
                ->addAttribute(['id' => 'edit_form_accounts_contact_name'])
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Hidden', 'client.form.edit.id')
                ->setElementName('client_id')
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Hidden', 'client.form.edit.action')
                ->setElementName('action')
                ->setElementValue(1)
        );

        $this->addChildBlock(
            $this->createBlock('\DaveBaker\Form\Block\Input\Submit', 'client.form.edit.submit')
                ->setElementValue($editMode ? "Update Client" : "Add Client")
                ->setElementName('client_submit')
        );

    }
}