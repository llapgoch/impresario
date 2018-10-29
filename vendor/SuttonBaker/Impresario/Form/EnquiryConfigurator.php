<?php

namespace SuttonBaker\Impresario\Form;

use \SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;
/**
 * Class EnquiryConfigurator
 * @package SuttonBaker\Impresario\Form\Rules
 */
class EnquiryConfigurator
    extends \DaveBaker\Form\Validation\Rule\Configurator\Base
    implements \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface
{
    /**
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    protected function _collate()
    {
        $this->addRule(
            $this->createRule('Date', 'date_received', 'Date Received')
        );

        $this->addRule(
            $this->createRule('DateCompare\Past', 'date_received', 'Date Received')
        );

        $this->addRule(
            $this->createRule('Required', 'client_reference', 'Client Reference')
        );

        $this->addRule(
            $this->createRule('\SuttonBaker\Impresario\Form\Rule\Client', 'client_id', 'Client')
        );

        $this->addRule(
            $this->createRule('User', 'assigned_to_id', 'Assigned To')
        );

        $this->addRule(
            $this->createRule('Required', 'site_name', 'Site Name')
        );

        $this->addRule(
            $this->createRule('Date', 'target_date', 'Target Date')
        );

        $this->addRule(
            $this->createRule('Required', 'status', 'Status')
        );

        $this->addRule(
            $this->createRule('Required', 'client_requested_by', 'Client Requested By')
        );


        // Conditional Rules
        $dateCompleted = $this->getValue('date_completed');
        $statusIsClosed = $this->getValue('status') == EnquiryDefinition::STATUS_COMPLETE;

        if($this->getValue('status') !== EnquiryDefinition::STATUS_OPEN){
            $this->addRule(
                $this->createRule('User', 'engineer_id', 'Engineer')
                ->setMainError('An engineer must be assigned for this enquiry\'s status')
            );
        }

        if($statusIsClosed || $this->getValue('status') == EnquiryDefinition::STATUS_INVOICED){
            $this->addRule(
                $this->createRule('DateCompare\Past', 'date_completed', 'Date Completed')
                ->setMainError('Date Completed must be chosen if this enquiry\'s status is set to \'Invoiced\' or \'Complete\'')
            );
        }

       if($this->getValue('date_completed')){
           $statusRule = $this->createRule('Custom', 'status', 'Status');
           $statusRule->setMainError('Status must be \'Invoiced\' or  \'Complete\' if \'Date Completed\' has been set')
               ->setInputError('This must be set to \'Complete\'');

           $this->addRule($statusRule->setValidationMethod(
               function($value, $ruleInstance) use($statusIsClosed) {

                   if($statusIsClosed == false
                        && $this->getValue('status') !== EnquiryDefinition::STATUS_INVOICED){
                       return $ruleInstance->createError();
                   }

                   return true;
               }
           ));
       }

    }
}