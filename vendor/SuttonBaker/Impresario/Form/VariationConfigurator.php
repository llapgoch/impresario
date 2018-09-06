<?php

namespace SuttonBaker\Impresario\Form;

/**
 * Class VariationConfigurator
 * @package SuttonBaker\Impresario\Form\Rules
 */
class VariationConfigurator
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
            $this->createRule('DateCompare\Past', 'date_approved', 'Date Approved')
        );

        $this->addRule(
            $this->createRule('Required', 'description', 'Description')
        );

        $this->addRule(
            $this->createRule('Numeric', 'value', 'Invoice Value')
        );

        $this->addRule(
            $this->createRule('Numeric', 'net_cost', 'Net Cost')
        );

        $this->addRule(
            $this->createRule('Numeric', 'net_sell', 'Net Sell')
        );
    }

}