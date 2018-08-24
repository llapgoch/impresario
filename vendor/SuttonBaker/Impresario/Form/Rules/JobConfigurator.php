<?php

namespace SuttonBaker\Impresario\Form\Rules;

class JobConfigurator
    extends \DaveBaker\Form\Validation\Rule\Configurator\Base
    implements \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface
{
    protected function _collate()
    {
        $this->addRule(
            $this->createRule('Required', 'horse', 'Horse')
        );

        $this->addRule(
            $this->createRule('Numeric', 'goose', 'Goose')
        );

        $this->addRule(
            $this->createRule('Numeric', 'moose', 'Moose')
        );

        $this->addRule(
            $this->createRule('Required', 'bio', 'Biography')
        );

        $this->addRule(
            $this->createRule('Date', 'date_field', 'Created Date')
        );

        $this->addRule(
            $this->createRule('NumericCompare\GreaterEqual', 'moose', 'Moose')
                ->setCompareNumber(100)
        );

    }
}