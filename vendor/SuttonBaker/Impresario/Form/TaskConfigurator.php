<?php

namespace SuttonBaker\Impresario\Form;
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

        if($this->getValue('status') == \SuttonBaker\Impresario\Definition\Task::STATUS_COMPLETE){
            $this->addRule(
                $this->createRule('User', 'completed_by_id', 'Completed By')
            );

            $this->addRule(
                $this->createRule('DateCompare\Past', 'date_completed', 'Date Completed')
            );
        }

    }
}