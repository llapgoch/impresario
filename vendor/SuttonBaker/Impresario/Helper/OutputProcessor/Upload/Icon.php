<?php

namespace SuttonBaker\Impresario\Helper\OutputProcessor\Upload;

use DaveBaker\Core\Helper\OutputProcessor\Base;
use DaveBaker\Core\Helper\OutputProcessor\OutputProcessorInterface;
use SuttonBaker\Impresario\Definition\Upload;

class Icon
    extends Base
    implements OutputProcessorInterface
{
    /**
     * @param $value
     * @return string
     */
    public function process($value)
    {
        $icon = Upload::getIcon($this->getModel()->getMimeType());
        return "<i class='{$icon} fa-2x'></i>";
    }
}