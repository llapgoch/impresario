<?php

namespace SuttonBaker\Impresario\Form;

/**
 * Class InvoiceConfigurator
 * @package SuttonBaker\Impresario\Form\Rules
 */
class InvoiceConfigurator
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
            $this->createRule('DateCompare\Past', 'invoice_date', 'Invoice Date')
        );

        $this->addRule(
            $this->createRule('Required', 'invoice_number', 'Invoice Number')
        );

        $this->addRule(
            $this->createRule('Numeric', 'value', 'Invoice Value')
        );

    }

}