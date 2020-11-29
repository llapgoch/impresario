<?php

namespace SuttonBaker\Impresario\Form;
/**
 * Class SupplierConfigurator
 * @package SuttonBaker\Impresario\Form\Rules
 */
class SupplierConfigurator
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
            $this->createRule('Required', 'supplier_name', 'Name')
        );

        $this->addRule(
            $this->createRule('Required', 'address_line1', 'Address Line 1')
        );

        $this->addRule(
            $this->createRule('Required', 'postcode', 'Postcode')
        );

        $this->addRule(
            $this->createRule('Required', 'county', 'County')
        );

        $this->addRule(
            $this->createRule('Directory\Country', 'country_code', 'Country')
        );

        $this->addRule(
            $this->createRule('Required', 'supplier_contact_phone', 'Supplier Phone Number')
        );

        $this->addRule(
            $this->createRule('Required', 'supplier_contact', 'Supplier Contact Name')
        );

        $this->addRule(
            $this->createRule('Required', 'accounts_contact_phone', 'Accounts Phone Number')
        );

        $this->addRule(
            $this->createRule('Required', 'accounts_contact', 'Accounts Contact Name')
        );
    }
}