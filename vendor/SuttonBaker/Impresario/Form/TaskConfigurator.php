<?php

namespace SuttonBaker\Impresario\Form;

use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
/**
 * Class TaskConfigurator
 * @package SuttonBaker\Impresario\Form\Rules
 */
class TaskConfigurator
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
            $this->createRule('Required', 'status', 'Status')
        );

        $this->addRule(
            $this->createRule('Required', 'priority', 'Priority')
        );

        $this->addRule(
            $this->createRule('Date', 'target_date', 'Target Date')
        );

        $this->addRule(
            $this->createRule('User', 'assigned_to_id', 'Assigned To')
        );

        $this->addRule(
            $this->createRule('Required', 'description', 'Description')
        );


        // Conditional Rules
        $dateCompleted = $this->getValue('date_completed');
        $completedById = $this->getValue('completed_by_id');
        $statusIsClosed = $this->getValue('status') !== TaskDefinition::STATUS_OPEN;


        $dateCompletedRule = $this->createRule('Custom', 'date_completed', 'Date Completed');
        $dateCompletedRule->setMainError('\'{{niceName}}\' must be set if \'Completed By\' has been chosen or \'Status\' is Complete')
            ->setInputError('This must be set');

        $this->addRule($dateCompletedRule->setValidationMethod(
            function($value, $ruleInstance) use($completedById, $statusIsClosed) {
                if(($completedById && !$value) || ($statusIsClosed && !$value)){
                    return $ruleInstance->createError();
                }

                return true;
            }
        ));


        $completedRule = $this->createRule('Custom', 'completed_by_id', 'Completed By');
        $completedRule->setMainError('\'{{niceName}}\' must be set if \'Date Completed\' has been chosen or \'Status\' is Complete')
            ->setInputError('This must be set');

        $this->addRule($completedRule->setValidationMethod(
            function($value, $ruleInstance) use($dateCompleted, $statusIsClosed) {
                if(($dateCompleted && !$value) || ($statusIsClosed && !$value)){
                    return $ruleInstance->createError();
                }

                return true;
            }
        ));


        $statusRule = $this->createRule('Custom', 'status', 'Status');
        $statusRule->setMainError('\'{{niceName}}\' cannot be Open if \'Completed By\' or \'Date Completed\' is set')
            ->setInputError('This must not be \'Open\'');

        $this->addRule($statusRule->setValidationMethod(
            function($value, $ruleInstance) use($dateCompleted) {

                if($dateCompleted && $value == TaskDefinition::STATUS_OPEN){
                    return $ruleInstance->createError();
                }

                return true;
            }
        ));

    }
}