<?php

namespace SuttonBaker\Impresario\Form;

use SuttonBaker\Impresario\Definition\Variation;

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
            $this->createRule('Required', 'description', 'Description')
        );

        $this->addRule(
            $this->createRule('Numeric', 'value', 'Variation Sell')
        );

        $this->addRule(
            $this->createRule('Numeric', 'net_cost', 'Net Cost')
        );

        $statusIsClosed = $this->getValue('status') !== Variation::STATUS_OPEN;

        if($this->getValue('date_approved')) {
            $this->addRule(
                $this->createRule('DateCompare\Past', 'date_approved', 'Date Approved')
            );

            $statusRule = $this->createRule('Custom', 'status', 'Status');
            $statusRule->setMainError('Status must not be \'Open\' if \'Date Approved\' has been set')
                ->setInputError('This cannot be set to \'Open\'');

            $this->addRule($statusRule->setValidationMethod(
                function($value, $ruleInstance) use($statusIsClosed) {

                    if($statusIsClosed == false){
                        return $ruleInstance->createError();
                    }

                    return true;
                }
            ));

        }

        $netCost = $this->getValue('net_cost');
        $netSell = $this->getValue('value');

        $sellRule = $this->createRule('Custom', 'value', 'Invoice Value');
        $sellRule->setMainError('\'{{niceName}}\' cannot be lower than \'Net Cost\'')
            ->setInputError('This must be higher than Net Cost');

        if($this->getValue('status') == Variation::STATUS_APPROVED){
            $this->addRule(
                $this->createRule('DateCompare\Past', 'date_approved', 'Date Approved')
                    ->setInputError('This must be set if status is \'Approved\'')
                    ->setMainError('\'{{niceName}}\' must be set if status is \'Approved\'')
            );
        }



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