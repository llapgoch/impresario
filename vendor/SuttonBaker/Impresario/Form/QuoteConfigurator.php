<?php

namespace SuttonBaker\Impresario\Form;

use SuttonBaker\Impresario\Api\Quote;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
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
            $this->createRule('Required', 'project_name', 'Project Name')
        );

        $this->addRule(
            $this->createRule('Required', 'client_requested_by', 'Client Requested By')
        );

        $this->addRule(
            $this->createRule('Date', 'date_required', 'Required By Date')
        );

        $netCost = $this->getValue('net_cost');
        $netSell = $this->getValue('net_sell');

        $sellRule = $this->createRule('Custom', 'net_sell', 'Net Sell');
        $sellRule->setMainError('\'{{niceName}}\' cannot be lower than \'Net Cost\'')
            ->setInputError('This must be higher than the net cost');

        $this->addRule($sellRule->setValidationMethod(
            function($value, $ruleInstance) use($netCost) {

                if((float) $value < (float) $netCost){
                    return $ruleInstance->createError();
                }

                return true;
            }
        ));

        $quoteIsCompleted = in_array($this->getValue('tender_status'),
            [QuoteDefinition::TENDER_STATUS_WON, QuoteDefinition::TENDER_STATUS_CLOSED_OUT]);

        if($quoteIsCompleted){
            $this->addRule(
                $this->createRule('Custom', 'status', 'Status')
                    ->setMainError('\'{{niceName}}\' must be \'Quoted\' if Tender Status is \'Won\' or \'Lost\'')
                    ->setInputError('This must be \'Quoted\'')
                    ->setValidationMethod(function($value, $ruleInstance){
                        if($value !== QuoteDefinition::STATUS_QUOTED){
                            return $ruleInstance->createError();
                        }
                        return true;
                    }
                )
            );
        }

        if($this->getValue('date_returned')){
            $this->addRule(
                $this->createRule('Custom', 'status', 'Status')
                    ->setMainError('\'{{niceName}}\' must be \'Quoted\' if Returned Date has been set')
                    ->setInputError('This must be \'Quoted\'')
                    ->setValidationMethod(function($value, $ruleInstance){
                        if($value !== QuoteDefinition::STATUS_QUOTED){
                            return $ruleInstance->createError();
                        }
                        return true;
                    }
                )
            );
        }

        if($this->getValue('status') == QuoteDefinition::STATUS_QUOTED){
            $this->addRule(
                $this->createRule('Custom', 'date_returned', 'Returned Date')
                    ->setMainError('\'{{niceName}}\' must be set if the quote\'s status is \'Quoted\'')
                    ->setInputError('This must be set')
                    ->setValidationMethod(function($value, $ruleInstance){
                        if(!$value){
                            return $ruleInstance->createError();
                        }
                        return true;
                    }
                )
            );
        }

        if($this->getValue('status') == QuoteDefinition::STATUS_OPEN){
            $this->addRule(
                $this->createRule('Custom', 'date_completed', 'Date Completed')
                    ->setMainError('\'{{niceName}}\' must not be set if the quote\'s status is \'Open\'')
                    ->setInputError('This cannot have a value')
                    ->setValidationMethod(function($value, $ruleInstance){
                        if($value){
                            return $ruleInstance->createError();
                        }
                        return true;
                    }
                )
            );

            $this->addRule(
                $this->createRule('Custom', 'completed_by_id', 'Completed By')
                    ->setMainError('\'{{niceName}}\' must not be set if the quote\'s status is \'Open\'')
                    ->setInputError('This cannot have a value')
                    ->setValidationMethod(function($value, $ruleInstance){
                        if($value){
                            return $ruleInstance->createError();
                        }
                        return true;
                    }
                )
            );
        }

        if($this->getValue('completed_by_id') || $this->getValue('date_completed')){
            $this->addRule(
                $this->createRule('Custom', 'status', 'Status')
                    ->setMainError('\'{{niceName}}\' cannot be open if \'Completed By\' or \'Completion Date\' have been set\'')
                    ->setInputError('This cannot be \'open\'')
                    ->setValidationMethod(function($value, $ruleInstance){
                        if($value == QuoteDefinition::STATUS_OPEN){
                            return $ruleInstance->createError();
                        }
                        return true;
                    }
                )
            );
        }

        if($this->getValue('status') == QuoteDefinition::STATUS_QUOTED){
            $this->addRule(
                $this->createRule('Numeric', 'net_cost', 'Net Cost')
                ->setMainError('\'{{niceName}}\' must be set if the quote\'s status is \'Quoted\'')
                ->setInputError('This requires a numeric value')
            );


            $this->addRule(
                $this->createRule('Numeric', 'net_sell', 'Net Sell')
                    ->setMainError('\'{{niceName}}\' must be a number if the quote\'s status is \'Quoted\'')
                    ->setInputError('This requires a numeric value')
            );

            $this->addRule(
                $this->createRule('User', 'estimator_id', 'Estimator')
                    ->setMainError('\'{{niceName}}\' must be assigned if a quote\'s status is \'Quoted\'')
                    ->setInputError('Please select an estimator')
            );
        }else{
            if($netCost){
                $this->addRule(
                    $this->createRule('Numeric', 'net_cost', 'Net Cost')
                );
            }

            if($netSell){
                $this->addRule(
                    $this->createRule('Numeric', 'net_sell', 'Net Sell')
                );
            }
        }


        if($this->getValue('date_returned')) {
            $this->addRule(
                $this->createRule('DateCompare\Past', 'date_returned', 'Date Returned')
            );
        }

        if($this->getValue('date_completed')) {
            $this->addRule(
                $this->createRule('DateCompare\Past', 'date_completed', 'Date Completed')
            );
        }

        if($this->getValue('completed_by_id')) {
            $this->addRule(
                $this->createRule('User', 'completed_by_id', 'Completed By')
            );
        }

        // Conditional Rules
        $dateCompleted = $this->getValue('date_completed');
        $completedById = $this->getValue('completed_by_id');


        if($quoteIsCompleted){
            $this->addRule(
                $this->createRule('User', 'completed_by_id', 'Completed By')
            );
        }

        if($quoteIsCompleted){
            $this->addRule(
                $this->createRule('DateCompare\Past', 'date_completed', 'Date Completed')
            );
        }
    }

}