<?php

namespace SuttonBaker\Impresario\Block\Client;
/**
 * Class ClientList
 * @package SuttonBaker\Impresario\Block\Client
 */
class ClientList
    extends \DaveBaker\Core\Block\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    public function getHtml()
    {
        return "CLIENT LIST BLOCK " . $this->getPageUrl('client_edit');
    }

}
