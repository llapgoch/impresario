<?php

namespace SuttonBaker\Impresario\Controller\Project;

use \SuttonBaker\Impresario\Controller\DownloadController;
use SuttonBaker\Impresario\Definition\Cost;
use SuttonBaker\Impresario\Definition\Invoice;
use SuttonBaker\Impresario\Definition\Page;
use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;

/**
 * Class SalesInvoiceDownloadController
 * @package SuttonBaker\Impresario\Controller\Project
 */
class VariationDownloadController
extends DownloadController
{
    const ENTITY_ID_PARAM = 'project_id';

    protected function getFileName()
    {
        $projectId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM);
        return "variations-project-$projectId.csv";
    }

    protected function outputFileContent()
    {
        if (!($instanceId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM))) {
            return $this->getResponse()->redirectReferer(
                $this->getUrlHelper()->getPageUrl(Page::PROJECT_LIST)
            );
        }

        $modelInstance = $this->getProjectHelper()->getProject($instanceId);

        if (!$modelInstance->getId()) {
            $this->addMessage('The project could not be found');

            return $this->getResponse()->redirectReferer(
                $this->getUrlHelper()->getPageUrl(Page::PROJECT_LIST)
            );
        }

        $instanceCollection = $this->getVariationHelper()->getVariationCollectionForProject(
            $modelInstance->getId()
        )->addOutputProcessors([
            'created_at' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'date_approved' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'status' => $this->getVariationHelper()->getStatusOutputProcessor(),
        ]);

        $headers = ProjectDefinition::VARIATION_REPORT_SINGLE_HEADERS;
        $output = fopen("php://output", "w");

        fputcsv($output, $headers);

        $instanceItems = $instanceCollection->getItems();

        if (!count($instanceItems)) {
            $this->addMessage('No variations have been created for the project');

            return $this->getResponse()->redirectReferer(
                $this->getUrlHelper()->getPageUrl(Page::PROJECT_LIST)
            );
        }

        foreach ($instanceItems as $item) {
            $fields = [];
            foreach ($headers as $key => $header) {
                $fields[] = $item->getOutputData($key);
            }

            fputcsv($output, $fields);
        }

        fclose($output);
    }
}
