<?php

namespace SuttonBaker\Impresario\Form;

/**
 * Class CostConfigurator
 * @package SuttonBaker\Impresario\Form\Rules
 */
class CostConfigurator
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
            $this->createRule('DateCompare\Past', 'cost_date', 'Cost Date')
        );

        $this->addRule(
            $this->createRule('Required', 'cost_invoice_type', 'Invoice Type')
        );

        $this->addRule(
            $this->createRule('Required', 'cost_number', 'Cost Number')
        );

        $this->addRule(
            $this->createRule('Numeric', 'value', 'Cost Value')
        );

    }

}