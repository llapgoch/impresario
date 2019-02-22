<?php

namespace SuttonBaker\Impresario\Controller\Project;

use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;
use \SuttonBaker\Impresario\Controller\DownloadController;
use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;

/**
 * Class ReportController
 * @package SuttonBaker\Impresario\Controller\Project
 */
class ReportController
    extends DownloadController
{
    protected function getFileName()
    {
        return 'project-report.csv';
    }

    protected function outputFileContent()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Project\Collection $instanceCollection */
        $instanceCollection = $this->getProjectHelper()->getProjectCollection()
        ->where('status<>?', ProjectDefinition::STATUS_COMPLETE)
        ->joinLeft(
            "{{client}}",
            "{{client}}.client_id={{project}}.client_id",
            ['client_name' => 'client_name']
        )
        ->addOutputProcessors([
            'project_start_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'project_end_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'status' => $this->getProjectHelper()->getStatusOutputProcessor()
        ]);
       

        $output = fopen("php://output", "w");

        fputcsv($output, ProjectDefinition::REPORT_HEADERS);
        
        foreach($instanceCollection->getItems() as $item){
            $fields = [];
            foreach(ProjectDefinition::REPORT_HEADERS as $key => $header){
                $fields[] = $item->getOutputData($key);
            }
            fputcsv($output, $fields);
        }
        
        fclose($output);
        
    }
}