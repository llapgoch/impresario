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

        $netCost = $this->getValue('net_cost');
        $netSell = $this->getValue('value');

        $sellRule = $this->createRule('Custom', 'value', 'Invoice Value');
        $sellRule->setMainError('\'{{niceName}}\' cannot be lower than \'Net Cost\'')
            ->setInputError('This must be higher than Net Cost');

        $this->addRule($sellRule->setValidationMethod(
            function($value, $ruleInstance) use($netCost) {

                if((float) $value < (float) $netCost){
                    return $ruleInstance->createError();
                }

                return true;
            }
        ));
    }

}