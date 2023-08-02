<?php

namespace SuttonBaker\Impresario\Controller\Quote;

use \SuttonBaker\Impresario\Controller\DownloadController;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;

/**
 * Class EnquiryListController
 * @package SuttonBaker\Impresario\Controller\Enquiry
 */
class ReportController
    extends DownloadController
{
    protected function getFileName()
    {
        return 'quote-report.csv';
    }

    protected function outputFileContent()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $instanceCollection */
        $instanceCollection = $this->getQuoteHelper()->getDisplayQuotes(true, false)
        ->addOutputProcessors([
            'date_returned' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'date_completed' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'target_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'date_received' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'status' => $this->getQuoteHelper()->getStatusOutputProcessor(),
            'revision_number' => $this->getQuoteHelper()->getRevisionOutputProcessor(),
            'tender_status' => $this->getQuoteHelper()->getTenderStatusOutputProcessor(),
            'priority' => $this->getQuoteHelper()->getPriorityOutputProcessor()
        ]);

        $output = fopen("php://output", "w");

        fputcsv($output, QuoteDefinition::REPORT_HEADERS);
        
        foreach($instanceCollection->getItems() as $item){
            $fields = [];
            foreach(QuoteDefinition::REPORT_HEADERS as $key => $header){
                $fields[] = $item->getOutputData($key);
            }
            fputcsv($output, $fields);
        }
        
        fclose($output);
        
    }
}