<?php

namespace SuttonBaker\Impresario\Block\Enquiry\Form;

use \SuttonBaker\Impresario\Definition\Enquiry;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Client\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'enquiry_id';
    const PREFIX_KEY = 'enquiry';
    const PREFIX_NAME = 'Enquiry';

    /**
     * @return \SuttonBaker\Impresario\Block\Form\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\SelectConnector\Exception
     * @throws \Zend_Db_Select_Exception
     */
    protected function _preDispatch()
    {
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;
        $entityId = $this->getRequest()->getParam(self::ID_KEY);
        $editMode = false;


        if($entityId){
            /** @var \SuttonBaker\Impresario\Model\Db\Enquiry $entityInstance */
            $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Enquiry')->load($entityId);

            $editMode = true;

            $quoteEntity = $entityInstance->getQuoteEntity();
            $urlParams = [];

            if($quoteEntity->getId()){
                $urlParams['quote_id'] = $quoteEntity->getId();
            }else{
                $urlParams['enquiry_id'] = $entityId;
            }

            $this->addChildBlock(
                $quoteLink = $this->createBlock('\DaveBaker\Core\Block\Html\Tag', 'create.quote')
                    ->setTag('a')
                    ->setTagText($quoteEntity->getId() ? 'View Quote' : 'Create Quote')
                    ->addAttribute(
                        ['href' => $this->getPageUrl(
                            \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT,
                            $urlParams,
                            $this->getApp()->getHelper('Url')->getCurrentUrl()
                        )]
                    )
            );

            $this->taskTableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\TaskTable',
                "{$prefixKey}.task.table"
            );

            $this->taskTableBlock->setInstanceCollection(
                $this->getTaskHelper()->getTaskCollectionForEntity(
                    $entityId,
                    TaskDefinition::TASK_TYPE_ENQUIRY,
                    TaskDefinition::STATUS_OPEN
                )
            )->setEditLinkParams([
                \DaveBaker\Core\App\Request::RETURN_URL_PARAM => $this->getApp()->getRequest()->createReturnUrlParam()
            ]);

            $this->addChildBlock($this->taskTableBlock);
        }

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName('enquiry_edit');

        $elements = $builder->build([
            [
                'name' => 'date_received',
                'labelName' => 'Date Received',
                'formGroup' => true,
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => ['readonly' => 'readonly', 'autocomplete' => 'off'],
                'value' => $this->getApp()->getHelper('Date')->currentDateShortLocalOutput()
            ], [
                'name' => 'client_reference',
                'labelName' => 'Client Reference',
                'formGroup' => true,
                'type' => 'Input\Text',
                'attributes' => ['autocomplete' => 'off']
            ], [
                'name' => 'client_id',
                'labelName' => 'Client',
                'formGroup' => true,
                'type' => 'Select'
            ], [
                'name' => 'project_manager_id',
                'labelName' => 'Project Manager',
                'type' => 'Select',
                'formGroup' => true,

            ], [
                'name' => 'engineer_id',
                'labelName' => 'Engineer',
                'type' => 'Select',
                'formGroup' => true,
            ], [
                'name' => 'site_name',
                'labelName' => 'Site Name',
                'type' => 'Input\Text',
                'formGroup' => true,
            ], [
                'name' => 'target_date',
                'labelName' => 'Target Date',
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'formGroup' => true,
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '0', 'maxDate' => "+5Y"]
                    )]
            ], [
                'name' => 'notes',
                'labelName' => 'Notes',
                'formGroup' => true,
                'type' => 'TextArea'
            ], [
                'name' => 'status',
                'labelName' => 'Enquiry Status',
                'formGroup' => true,
                'type' => 'Select'
            ], [
                'name' => 'completed_by_id',
                'labelName' => 'Completed By',
                'formGroup' => true,
                'type' => 'Select'
            ], [
                'name' => 'date_completed',
                'labelName' => 'Date Completed',
                'formGroup' => true,
                'class' => 'js-date-picker',
                'type' => 'Input\Text',
                'attributes' => [
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(
                        ['minDate' => '', 'maxDate' => "0"]
                    )]
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => ['button_name' => $editMode ? 'Update Enquiry' : 'Create Enquiry']
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

        $this->addChildBlock(array_values($elements));

//        foreach($this->getValueFormElements() as $element){
//            var_dump($element->getName());
//        }


        // Set up special values

        // Client
//        $clients = $this->getClientHelper()->getClientCollection();
//        $this->createCollectionSelectConnector()
//            ->configure($clients, 'client_id', 'client_name', $elements['client_id_element']);
//
//
//        // Project Manager
//        $projectManagers = $this->getApp()->getHelper('User')->getUserCollection();
//        $this->createCollectionSelectConnector()
//            ->configure($projectManagers, 'ID', 'user_login', $elements['project_manager_id_element']);
//
//        // Engineer
//        $engineers = $this->getApp()->getHelper('User')->getUserCollection();
//        $this->createCollectionSelectConnector()
//            ->configure($engineers, 'ID', 'user_login', $elements['engineer_id_element']);
//
//        // Completed by Users
//        $completedUsers = $this->getApp()->getHelper('User')->getUserCollection();
//        $this->createCollectionSelectConnector()
//            ->configure($completedUsers, 'ID', 'user_login', $elements['completed_by_id_element']);
//
//        // Statuses
//        $this->createArraySelectConnector()
//            ->configure(Enquiry::getStatuses(), $elements['status_element']);
//
//        $elements['status_element']->setShowFirstOption(false);



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