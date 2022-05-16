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
    /** @var SuttonBaker\Impresario\Model\Db\Cost */
    protected $model;
    /**
     *
     * @param SuttonBaker\Impresario\Model\Db\Cost $model
     * @return $this
     */
    public function setModel(
        \SuttonBaker\Impresario\Model\Db\Cost $model
    ) {
        $this->model = $model;
        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    protected function _collate()
    {
        if (!$this->getModel()) {
            throw new \Exception("Model must be set on cost configurator");
        }

        $this->addRule(
            $this->createRule('DateCompare\Past', 'cost_date', 'Cost Date')
        );

        $this->addRule(
            $this->createRule('Date', 'delivery_date', 'Delivery Date')
        );


        $this->addRule(
            $this->createRule('Required', 'cost_invoice_type', 'Invoice Type')
        );

        $this->addRule(
            $this->createRule('Required', 'supplier_id', 'Supplier')
        );

        $this->addRule(
            $this->createRule('Required', 'cost_number', 'Cost Number')
        );

        $this->addRule(
            $this->createRule('Required', 'status', 'Status')
        );

        $itemRule = $this->createRule('Custom', 'po_items', 'Po Items')
            ->setMainError('Please ensure all PO Items contain a description, qty, and unit price');

        $this->addRule($itemRule->setValidationMethod(
            function ($costItems, $ruleInstance) use ($itemRule) {
                
                if (!is_array($costItems)) {
                    return $ruleInstance->createError();
                }

                $required = ['description', 'qty', 'unit_price', 'id', 'removed'];

                foreach ($costItems as $costItem) {
                    if (!is_array($costItem)) {
                        return $ruleInstance->createError();
                    }
    
                    foreach ($required as $requirement) {
                        if (!array_key_exists($requirement, $costItem)) {
                            return $ruleInstance->createError();
                        }
                    }

                    if((bool) $costItem['removed']) {
                        continue;
                    }

                    if(!trim($costItem['description'])) {
                        $itemRule->setMainError('Please ensure each PO Item has a description');
                        return $ruleInstance->createError();
                    }

                    if (!is_numeric($costItem['qty'])
                        || !is_numeric($costItem['unit_price'])
                    ) {
                        $itemRule->setMainError('Please ensure each PO Item has a valid quantity and unit price');
                        return $ruleInstance->createError();
                    }

                    if((float) $costItem['qty'] < 1) {
                        $itemRule->setMainError('Please ensure each PO Item has a positive quantity');
                        return $ruleInstance->createError();
                    }
                }

                return true;
            }
        ));
    }
}
