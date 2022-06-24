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
class CostInvoiceDownloadController
extends DownloadController
{
    const ENTITY_ID_PARAM = 'project_id';

    protected function getFileName()
    {
        $projectId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM);
        return "purchase-orders-project-$projectId.csv";
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

        $instanceCollection = $this->getCostHelper()->getCostCollectionForEntity(
            $modelInstance->getId(),
            Cost::COST_TYPE_PROJECT
        )->addOutputProcessors([
            'cost_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'delivery_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
            'status' => $this->getCostHelper()->getCostStatusOutputProcessor(),
            'cost_invoice_type' => $this->getCostHelper()->getCostInvoiceTypeOutputProcessor(),
        ]);

        $headers = ProjectDefinition::COST_INVOICE_REPORT_SINGLE_HEADERS;
        $output = fopen("php://output", "w");

        fputcsv($output, $headers);

        $instanceItems = $instanceCollection->getItems();

        if (!count($instanceItems)) {

            $this->addMessage('No purchase orders have been created for the project');

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
