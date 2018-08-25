<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Layout
 */
class Client extends \DaveBaker\Core\Layout\Base
{

    /**
     * @throws \DaveBaker\Core\App\Exception
     */
    public function clientEditHandle()
    {
        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Client\Form\Edit',
                'client.form.edit'
            )->setElementName('client_edit_form')->setShortcode('body_content')->setFormAction("")

        );
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     */
    public function clientListHandle()
    {
        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Client\ClientList',
                'client.list'
            )->setShortcode('body_content')
        );
    }
}