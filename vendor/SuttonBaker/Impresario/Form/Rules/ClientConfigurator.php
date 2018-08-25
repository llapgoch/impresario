<?php

namespace SuttonBaker\Impresario\Form\Rules;
/**
 * Class JobConfigurator
 * @package SuttonBaker\Impresario\Form\Rules
 */
class ClientConfigurator
    extends \DaveBaker\Form\Validation\Rule\Configurator\Base
    implements \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface
{
    /**
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    protected function _collate()
    {
        $this->addRule(
            $this->createRule('Required', 'client_name', 'Name')
        );

        $this->addRule(
            $this->createRule('Required', 'address_line1', 'Address Line 1')
        );

        $this->addRule(
            $this->createRule('Required', 'postcode', 'Postcode')
        );

        $this->addRule(
            $this->createRule('Required', 'sales_contact_phone', 'Sales Phone Number')
        );

        $this->addRule(
            $this->createRule('Required', 'sales_contact', 'Sales Contact Name')
        );

        $this->addRule(
            $this->createRule('Required', 'accounts_contact_phone', 'Accounts Phone Number')
        );

        $this->addRule(
            $this->createRule('Required', 'accounts_contact', 'Accounts Contact Name')
        );
    }
}