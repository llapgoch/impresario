<?php

namespace SuttonBaker\Impresario\Controller\Enquiry;

use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;
use \SuttonBaker\Impresario\Controller\DownloadController;

/**
 * Class EnquiryListController
 * @package SuttonBaker\Impresario\Controller\Enquiry
 */
class ReportController
    extends DownloadController
{
    protected function getFileName()
    {
        return 'enquiry.csv';
    }

    protected function outputFileContent()
    {
        $output = fopen("php://output", "w");
        
        fputs($output, 'Test');
        
        fclose($output);
    }
}