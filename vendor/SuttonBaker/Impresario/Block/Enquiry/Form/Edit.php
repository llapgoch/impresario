<?php

namespace SuttonBaker\Impresario\Block\Enquiry\Form;

use \SuttonBaker\Impresario\Definition\Enquiry;

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
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\SelectConnector\Exception
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

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName('enquiry_edit');

        $elements = $builder->build([
            [
                'name' => 'date_received',
                'labelName' => 'Date Received',
                'class' => 'js-date-picker',
                'type' => 'Input\Text'
            ], [
                'name' => 'client_reference',
                'labelName' => 'Client Reference',
                'type' => 'Input\Text'
            ], [
                'name' => 'client_id',
                'labelName' => 'Client',
                'type' => 'Select'
            ], [
                'name' => 'owner_id',
                'labelName' => 'Owner',
                'type' => 'Select'
            ], [
                'name' => 'site_name',
                'labelName' => 'Site Name',
                'type' => 'Input\Text'
            ], [
                'name' => 'notes',
                'labelName' => 'Notes',
                'type' => 'TextArea'
            ],
            [
                'name' => 'status',
                'labelName' => 'Enquiry Status',
                'type' => 'Select'
            ],
            [
                'name' => 'completed_by_id',
                'labelName' => 'Completed By',
                'type' => 'Select'
            ],[
                'name' => 'submit',
                'type' => 'Input\Submit',
                'value' => 'Update Enquiry'
            ], [
                'name' => 'enquiry_id',
                'type' => 'Input\Hidden',
                'value' => $entityId
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ]
        ]);

        // Set up special values

        // Client
        $clients = $this->getClientHelper()->getClientCollection();
        $this->createCollectionSelectConnector()
            ->configure($clients, 'client_id', 'client_name', $elements['client_id_element']);


        // Owner
        $owners = $this->getApp()->getHelper('User')->getUserCollection();
        $this->createCollectionSelectConnector()
            ->configure($owners, 'ID', 'user_login', $elements['owner_id_element']);

        // Completed by Users
        $completedUsers = $this->getApp()->getHelper('User')->getUserCollection();
        $this->createCollectionSelectConnector()
            ->configure($completedUsers, 'ID', 'user_login', $elements['completed_by_id_element']);

        // Statuses
        $this->createArraySelectConnector()
            ->configure(Enquiry::getStatuses(), $elements['status_element']);

        $elements['status_element']->setShowFirstOption(false);


        $this->addChildBlock(array_values($elements));
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Client
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getClientHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Client');
    }

    /**
     * @return \DaveBaker\Form\SelectConnector\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createCollectionSelectConnector()
    {
        return $this->createAppObject('\DaveBaker\Form\SelectConnector\Collection');
    }

    /**
     * @return \DaveBaker\Form\SelectConnector\AssociativeArray
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createArraySelectConnector()
    {
        return $this->createAppObject('\DaveBaker\Form\SelectConnector\AssociativeArray');
    }
}