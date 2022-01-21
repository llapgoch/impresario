<?php

namespace SuttonBaker\Impresario\Controller\Enquiry;

use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;
use \SuttonBaker\Impresario\Controller\DownloadController;
use \SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;

/**
 * Class EnquiryListController
 * @package SuttonBaker\Impresario\Controller\Enquiry
 */
class ReportController
    extends DownloadController
{
    protected function getFileName()
    {
        return 'enquiry-report.csv';
    }

    protected function outputFileContent()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $enquiryCollection */
        $instanceCollection = $this->getEnquiryHelper()->getDisplayEnquiries()
        ->addOutputProcessors([
            'date_completed' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'target_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'date_received' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'status' => $this->getEnquiryHelper()->getStatusOutputProcessor()
        ]);

        $output = fopen("php://output", "w");

        fputcsv($output, EnquiryDefinition::REPORT_HEADERS);
        
        foreach($instanceCollection->getItems() as $item){
            $fields = [];
            foreach(EnquiryDefinition::REPORT_HEADERS as $key => $header){
                $fields[] = $item->getOutputData($key);
            }
            fputcsv($output, $fields);
        }
        
        fclose($output);
        
    }
}