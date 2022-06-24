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
class ReportController
extends DownloadController
{
    protected function getFileName()
    {
        return 'archive-report.csv';
    }

    protected function outputFileContent()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Project\Collection $instanceCollection */
        $instanceCollection = $this->getProjectHelper()->getProjectCollection()
            ->where('status=?', ProjectDefinition::STATUS_COMPLETE)

            ->addOutputProcessors([
                'project_start_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'project_end_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'status' => $this->getProjectHelper()->getStatusOutputProcessor()
            ]);

        $headers = ArchiveDefinition::REPORT_HEADERS;
        $variationsAmount = 0;

        // Get the number of variations for all projects
        /** @var \SuttonBaker\Impresario\Model\Db\Project $item */
        foreach ($instanceCollection->getItems() as $item) {
            $variationsAmount = max($variationsAmount, count($item->getVariations()->getItems()));
        }

        for ($i = 0; $i < $variationsAmount; $i++) {
            $num = $i + 1;
            foreach (ArchiveDefinition::VARIATION_REPORT_HEADERS as $key => $variationHeader) {
                $headers[$key . "_" . $num] = "Variation {$num}: $variationHeader";
            }
        }

        $output = fopen("php://output", "w");

        fputcsv($output, $headers);

        foreach ($instanceCollection->getItems() as $item) {
            $fields = [];
            foreach (ArchiveDefinition::REPORT_HEADERS as $key => $header) {
                $fields[] = $item->getOutputData($key);
            }

            $variationCollection = $item->getVariations();
            $variationCollection->addOutputProcessors([
                'status' => $this->createAppObject(
                    \SuttonBaker\Impresario\Helper\OutputProcessor\Variation\Status::class
                )
            ]);

            $variations = $variationCollection->getItems();

            for ($i = 0; $i <= $variationsAmount; $i++) {
                $variation = isset($variations[$i]) ? $variations[$i] : null;
                foreach (ArchiveDefinition::VARIATION_REPORT_HEADERS as $key => $variationHeader) {
                    $fields[] = $variation ? $variation->getOutputData($key) : "";
                }
            }

            fputcsv($output, $fields);
        }

        fclose($output);
    }
}
