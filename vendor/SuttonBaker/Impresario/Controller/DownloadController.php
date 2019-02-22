<?php

namespace SuttonBaker\Impresario\Controller;

use \DaveBaker\Core\Controller\Exception;
/**
 * Class DefaultController
 * @package SuttonBaker\Impresario\Controller
 */
abstract class DownloadController
    extends Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    /** @var string */
    protected $fileName;
    /** @var string */
    protected $contentType = 'text/csv';

    public function execute()
    {
        $this->outputHeaders();
        $this->outputFileContent();
        exit;
    }

    abstract protected function getFileName();
    abstract protected function outputFileContent();

    public function outputHeaders()
    {
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename={$this->getFileName()}");
        # Disable caching - HTTP 1.1
        header("Cache-Control: no-cache, no-store, must-revalidate");
        # Disable caching - HTTP 1.0
        header("Pragma: no-cache");
        # Disable caching - Proxies
        header("Expires: 0");
    }
}