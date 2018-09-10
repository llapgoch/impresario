<?php

namespace SuttonBaker\Impresario\Api;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Client
 * @package SuttonBaker\Impresario\Api
 *
 */
class Client
    extends \DaveBaker\Core\Api\Base
{
    /**
     * @param $params
     * @param $request
     * @return array
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function updateAction($params, $request)
    {
        $tag = $this->getApp()->getBlockManager()->createBlock(
            '\DaveBaker\Core\Block\Html\Tag',
            'enquiry.edit.form.errors'
        )->setTagText("WOOOOO");

        $this->addReplacerBlock($tag);
    }

}