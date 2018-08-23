<?php

namespace SuttonBaker\Impresario\Form\Rules;

class JobConfigurator
    extends \DaveBaker\Form\Validation\Rule\Configurator\Base
    implements \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface
{
    public function _collate()
    {
        $this->addRule(
            $this->createRule('Required')->configure('horse', 'Horse', $this->getValue('horse'))
        );

        $this->addRule(
            $this->createRule('Numeric')->configure('goose', 'Goose', $this->getValue('goose'))
        );

        $this->addRule(
            $this->createRule('Numeric')->configure('moose', 'Moose', $this->getValue('moose'))
        );

        $this->addRule(
            $this->createRule('NumberCompare\GreaterEqual')
                ->configure('moose', 'Moose', $this->getValue('moose'))
                ->setCompareNumber(100)
        );



//        $this->addRule(
//            $this->createRule('Numeric')->configure('age', 'Age', $this->getValue('age'))
//        );

    }
}