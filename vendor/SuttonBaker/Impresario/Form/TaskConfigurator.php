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
        $statusIsClosed = $this->getValue('status') == TaskDefinition::STATUS_COMPLETE;

        if($statusIsClosed || $this->getValue('date_completed')){
            $this->addRule(
                $this->createRule('User', 'completed_by_id', 'Completed By')
            );
        }

        if($statusIsClosed || $this->getValue('completed_by_id')){
            $this->addRule(
                $this->createRule('DateCompare\Past', 'date_completed', 'Date Completed')
            );
        }

        if($this->getValue('completed_by_id') || $this->getValue('date_completed')){
            $statusRule = $this->createRule('Custom', 'status', 'Status');
            $statusRule->setMainError('Status must not be \'Open\' if \'Completed By\' or \'Date Completed\' have been set')
                ->setInputError('This must be set to \'Complete\'');

            $this->addRule($statusRule->setValidationMethod(
                function($value, $ruleInstance) use($statusIsClosed) {

                    if($statusIsClosed == false){
                        return $ruleInstance->createError();
                    }

                    return true;
                }
            ));
        }

    }
}