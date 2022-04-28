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
            $this->createRule('Required', 'cost_invoice_type', 'Invoice Type')
        );

        $this->addRule(
            $this->createRule('Required', 'supplier_id', 'Supplier')
        );

        $this->addRule(
            $this->createRule('Required', 'cost_number', 'Cost Number')
        );

        $this->addRule(
            $this->createRule('Numeric', 'value', 'Cost Value')
        );
    }
}
