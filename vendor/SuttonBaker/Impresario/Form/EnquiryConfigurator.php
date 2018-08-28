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
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    protected function _collate()
    {
        $this->addRule(
            $this->createRule('Date', 'date_received', 'Date Received')
        );

        $this->addRule(
            $this->createRule('Required', 'client_reference', 'Client Reference')
        );

        $this->addRule(
            $this->createRule('\SuttonBaker\Impresario\Form\Rule\Client', 'client_id', 'Client')
        );

        $this->addRule(
            $this->createRule('User', 'owner_id', 'Owner')
        );

        $this->addRule(
            $this->createRule('Required', 'site_name', 'Site Name')
        );

    }
}