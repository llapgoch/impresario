<?php

namespace SuttonBaker\Impresario\Controller\Archive;

use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;
use \SuttonBaker\Impresario\Controller\DownloadController;
use \SuttonBaker\Impresario\Definition\Archive as ArchiveDefinition;
use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;

/**
 * Class ReportController
 * @package SuttonBaker\Impresario\Controller\Archive
 */
class ReportQuoteController
    extends DownloadController
{
    protected function getFileName()
    {
        return 'archive-quote-report.csv';
    }

    protected function outputFileContent()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Archive\Collection $instanceCollection */
        $instanceCollection = $this->getProjectHelper()->getProjectCollection()
        ->where('status=?', ProjectDefinition::STATUS_COMPLETE)
   
        ->addOutputProcessors([
            'project_start_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'project_end_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'status' => $this->getProjectHelper()->getStatusOutputProcessor()
        ]);

        $output = fopen("php://output", "w");

        fputcsv($output, ArchiveDefinition::REPORT_HEADERS);
        
        foreach($instanceCollection->getItems() as $item){
            $fields = [];
            foreach(ArchiveDefinition::REPORT_HEADERS as $key => $header){
                $fields[] = $item->getOutputData($key);
            }
            fputcsv($output, $fields);
        }
        
        fclose($output);
        
    }
}