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
            )->setShortcode('body_content')
        );
    }
}