<?php

namespace SuttonBaker\Impresario\Event;

use DaveBaker\Core\Api\Core\File;

class Upload
    extends \DaveBaker\Core\Base
{
    /**
     * @return Base
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addEvents();
    }

    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function addEvents()
    {
        // Hook into this event to add our block data
        $this->addEvent(
            'file_upload_api_upload_complete',
            function($context){
                /** @var File $apiFile */
                $apiFile = $context->getObject();

                $apiFile->addReplacerBlock()
            }

        );
    }
}