<?php

namespace SuttonBaker\Impresario\Form;

/**
 * Class ProjectConfigurator
 * @package SuttonBaker\Impresario\Form\Rules
 */
class ProjectConfigurator
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
            $this->createRule('Required', 'client_id', 'Client')
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
            $this->createRule('Date', 'date_required', 'Required By Date')
        );

        $this->addRule(
            $this->createRule('User', 'project_manager_id', 'Project Manager')
        );

        $this->addRule(
            $this->createRule('User', 'assigned_foreman_id', 'Foreman')
        );

        $this->addRule(
            $this->createRule('Numeric', 'net_cost', 'Net Cost')
        );

        $this->addRule(
            $this->createRule('Numeric', 'net_sell', 'Net Sell')
        );

        $this->addRule(
            $this->createRule('Numeric', 'actual_cost', 'Actual Cost')
        );


        $this->addRule(
            $this->createRule('Date', 'project_start_date', 'Project Start Date')
        );

        $this->addRule(
            $this->createRule('Date', 'project_end_date', 'Project End Date')
        );

        $this->addRule(
            $this->createRule('Required', 'status', 'Status')
        );

        $netCost = $this->getValue('net_cost');
        $netSell = $this->getValue('net_sell');

        $sellRule = $this->createRule('Custom', 'net_sell', 'Net Sell');
        $sellRule->setMainError('\'{{niceName}}\' cannot be lower than \'Net Cost\'')
            ->setInputError('This must be higher than Net Cost');

        $this->addRule($sellRule->setValidationMethod(
            function($value, $ruleInstance) use($netCost) {

                if((float) $value < (float) $netCost){
                    return $ruleInstance->createError();
                }

                return true;
            }
        ));

    }

}