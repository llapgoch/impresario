<?php

namespace SuttonBaker\Impresario\Form;

use SuttonBaker\Impresario\Definition\Project;

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
            $this->createRule('Date', 'date_required', 'Required By Date')
        );

        if($this->getValue('assigned_foreman_id')) {
            $this->addRule(
                $this->createRule('User', 'assigned_foreman_id', 'Foreman')
            );
        }

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

        $sellRule = $this->createRule('Custom', 'net_sell', 'Net Sell')
            ->setMainError('\'{{niceName}}\' cannot be lower than \'Net Cost\'')
            ->setInputError('This must be higher than Net Cost');

        $this->addRule($sellRule->setValidationMethod(
            function($value, $ruleInstance) use($netCost) {

                if((float) $value < (float) $netCost){
                    return $ruleInstance->createError();
                }

                return true;
            }
        ));

        /** @var \SuttonBaker\Impresario\Model\Db\Project $modelInstance */
        $modelInstance = $this->getApp()->getRegistry()->get('model_instance');

        if($this->getValue('status') !== Project::STATUS_OPEN){
            $this->addRule(
                $this->createRule('User', 'project_manager_id', 'Project Manager')
                    ->setMainError('\'{{niceName}}\' must be set if a project is not open')
            );
        }

        if($this->getValue('status') == Project::STATUS_COMPLETE){
            $this->addRule(
                $this->createRule('Custom', 'status', 'Status')
                ->setMainError('This Project can\'t be marked as complete as there is still an amount remaining to be invoiced')
                ->setInputError('This can\' be marked as complete yet')
                ->setValidationMethod(function($value, $ruleInstance) use ($modelInstance){
                    if($modelInstance->getInvoiceAmountRemaining() > 0){
                        return $ruleInstance->createError();
                    }

                    return true;
                })
            );
        }
    }

}