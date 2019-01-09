<?php

namespace SuttonBaker\Impresario\Helper\OutputProcessor\Upload;

use DaveBaker\Core\Definitions\Roles;
use DaveBaker\Core\Helper\OutputProcessor\Base;
use DaveBaker\Core\Helper\OutputProcessor\OutputProcessorInterface;
use DaveBaker\Core\Definitions\Api;
use SuttonBaker\Impresario\Definition\Upload;


class Remove
    extends Base
    implements OutputProcessorInterface
{
    /**
     * @param $value
     * @return string
     */
    public function process($value)
    {
        if(!$this->getUserHelper()->hasCapability(Roles::CAP_UPLOAD_FILE_REMOVE)){
            return '';
        }

        if(($this->getModel()->getCreatedById() !== $this->getUserHelper()->getCurrentUserId())
            && $this->getUserHelper()->hasCapability(Roles::CAP_UPLOAD_FILE_REMOVE_ANY) == false){
                return '';
        }

        $button = $this->getApp()->getBlockManager()->createBlock(
            \DaveBaker\Core\Block\Html\Tag::class
        )->setJsDataItems([
            'type' => 'File',
            'endpoint' => $this->getUrlHelper()->getApiUrl(
                Api::ENDPOINT_FILE_UPLOAD_REMOVE,
                ['id' => $this->getModel()->getId()]
            ),
            'returnUrl' => ''
        ])->setTag('a')
            ->setTagText('Remove')
            ->addClass('btn btn-red btn-sm js-delete-confirm js-delete-confirm-file');

        return $button->render();        
    }
}