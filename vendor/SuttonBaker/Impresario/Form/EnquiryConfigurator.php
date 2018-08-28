<?php

namespace SuttonBaker\Impresario\Form;
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
            $this->createRule('User', 'project_manager_id', 'Project Manager')
        );

        $this->addRule(
            $this->createRule('User', 'engineer_id', 'Engineer')
        );

        $this->addRule(
            $this->createRule('Required', 'site_name', 'Site Name')
        );

        $this->addRule(
            $this->createRule('Date', 'target_date', 'Target Date')
        );


        // Conditional Rules

        if($this->getValue('status') == \SuttonBaker\Impresario\Definition\Enquiry::STATUS_COMPLETE){
            $this->addRule(
                $this->createRule('User', 'completed_by_id', 'Completed By')
            );
        }

    }
}