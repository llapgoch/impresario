<?php

namespace SuttonBaker\Impresario\Config;
/**
 * Class Element
 * @package SuttonBaker\Impresario\Config
 */
class General extends \DaveBaker\Core\Config\General
{
    public function __construct()
    {
        $this->mergeConfig([
            'uploadAllowedMimeTypes' => 'image/jpeg, 
            image/png, 
            image/gif, 
            image/bmp,
            image/tiff,
            image/gif,
            image/svg,
            application/pdf,
            application/rtf,
            text/richtext,
            text/plain,
            text/rtf,
            text/html,
            appliation/x-msg,
            application/outlook,
            application/CDFV2-corrupt,
            application/CDFV2-unknown,
            message/rfc822,
            zz-application/zz-winassoc-MSG,
            application/vnd.openxmlformats-officedocument.wordprocessingml.document,
            application/msword,
            application/vnd.ms-outlook
            application/vnd.ms-word.document.macroenabled.12,
            application/vnd.ms-excel,
            application/vnd.ms-excel.sheet.macroenabled.12,
            application/vnd.ms-excel.sheet.macroEnabled.12,
            application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
            application/vnd.openxmlformats-officedocument.presentationml.presentation,
            application/vnd.ms-powerpoint,
            application/x-msaccess'
        ]);
    }
}