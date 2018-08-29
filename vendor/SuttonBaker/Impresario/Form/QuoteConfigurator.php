<?php

namespace SuttonBaker\Impresario\Form;
/**
 * Class QuoteConfigurator
 * @package SuttonBaker\Impresario\Form\Rules
 */
class QuoteConfigurator
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
            $this->createRule('DateCompare\Past', 'date_received', 'Date Received')
        );

        $this->addRule(
            $this->createRule('\SuttonBaker\Impresario\Form\Rule\Client', 'client_id', 'Client')
        );

        $this->addRule(
            $this->createRule('Required', 'project_name', 'Project Name')
        );

        $this->addRule(
            $this->createRule('Required', 'site_name', 'Site Name')
        );
        $this->addRule(
            $this->createRule('Required', 'client_requested_by', 'Client Requested By')
        );

        $this->addRule(
            $this->createRule('Required', 'client_reference', 'Client Reference')
        );

        $this->addRule(
            $this->createRule('Date', 'date_required', 'Date Required')
        );

        $this->addRule(
            $this->createRule('User', 'project_manager_id', 'Project Manager')
        );

        $this->addRule(
            $this->createRule('User', 'estimator_id', 'Estimator')
        );

        $this->addRule(
            $this->createRule('Date', 'date_return_by', 'Return By Date')
        );

        $this->addRule(
            $this->createRule('Number', 'net_cost', 'Net Cost')
        );

        $this->addRule(
            $this->createRule('Number', 'net_sell', 'Net Sell')
        );

        if($this->getValue('date_returned')) {
            $this->addRule(
                $this->createRule('DateCompare\Past', 'date_returned', 'Date Returned')
            );
        }

        $this->addRule(
            $this->createRule('Required', 'status', 'Status')
        );


        // Conditional Rules

        if($this->getValue('status') == \SuttonBaker\Impresario\Definition\Quote::STATUS_COMPLETE){
            $this->addRule(
                $this->createRule('User', 'completed_by_id', 'Completed By')
            );

            $this->addRule(
                $this->createRule('DateCompare\Past', 'date_completed', 'Date Completed')
            );
        }

    }
}