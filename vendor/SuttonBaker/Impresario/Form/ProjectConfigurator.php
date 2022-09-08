<?php

namespace SuttonBaker\Impresario\Form;

use SuttonBaker\Impresario\Definition\Project;
use SuttonBaker\Impresario\Definition\Upload;

/**
 * Class ProjectConfigurator
 * @package SuttonBaker\Impresario\Form\Rules
 */
class ProjectConfigurator
extends \DaveBaker\Form\Validation\Rule\Configurator\Base
implements \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface
{
    /**
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    protected function _collate()
    {
        $projectStarted = in_array($this->getValue('status'), [
            Project::STATUS_ON_SITE,
            Project::STATUS_RECALL,
            Project::STATUS_ON_SITE_VRF_SUBMITTED,
            Project::STATUS_READY_TO_INVOICE,
            Project::STATUS_READY_TO_SHUTDOWN,
            Project::STATUS_COMPLETE
        ]);

        // If complete, these items must have a value
        $checklistItems = [
            "checklist_plant_off_hired" => "All Plant Off Hired",
            "checklist_cost_invoice_received_logged" => "All expected cost invoices received and logged",
            "checklist_rams_qhse_filing" => "Completed RAMS sent to QHSE for filing",
            "checklist_customer_satisfaction_survey_logged" => "Customer satisfaction survey logged",
            "checklist_completion_photos_logged" => "Completion photos logged",
            "checklist_warranty_guarantee_certificate_filed" => "Any warranty / guarantee certification filed",
            "checklist_client_advised_operational_maintenance" => "Client advised on any operational & maintenance requirements",
            "checklist_client_crm_updated" => "Client CRM system updated",
        ];

        $this->addRule(
            $this->createRule('DateCompare\Past', 'date_received', 'Date Received')
        );

        $this->addRule(
            $this->createRule('Required', 'project_name', 'Project Name')
        );

        $this->addRule(
            $this->createRule('\SuttonBaker\Impresario\Form\Rule\Client', 'client_id', 'Client')
        );

        $this->addRule(
            $this->createRule('Date', 'date_required', 'Required By Date')
        );

        $this->addRule(
            $this->createRule('Numeric', 'rebate_percentage', 'Rebate %')
        );

        if((bool)$this->getValue('has_rebate')) {
            $rebateRule = $this->createRule('Custom', 'rebate_percentage', 'Rebate Percentage')
            ->setMainError('\'{{niceName}}\' must be between 0 - 100')
            ->setInputError('This must be between 0 - 100');

            $rebateRule->setValidationMethod(function($value, $ruleInstance) {
                $value = (float) $value;

                if($value < 0 || $value > 100){
                    return $ruleInstance->createError();
                }

                return true;
            });

            $this->addRule($rebateRule);
        }

        if ($projectStarted){
            $this->addRule(
                $this->createRule('Date', 'project_start_date', 'Project Start Date')
                    ->setMainError('\'{{niceName}}\' must be set if a project\'s status is on-site or complete')
            );

            $this->addRule(
                $this->createRule('Date', 'project_end_date', 'Project End Date')
                    ->setMainError('\'{{niceName}}\' must be set if a project\'s status is on-site or complete')
            );


            $this->addRule(
                $this->createRule('User', 'assigned_foreman_id', 'Foreman')
                    ->setMainError('\'{{niceName}}\' must be set if a project\'s status is on-site or complete')
            );

            $this->addRule(
                $this->createRule('User', 'project_manager_id', 'Contracts Manager')
                    ->setMainError('\'{{niceName}}\' must be set if a project\'s status is on-site or complete')
            );
        }

        $this->addRule(
            $this->createRule('Required', 'status', 'Status')
        );

        $netCost = $this->getValue('net_cost');
        $netSell = $this->getValue('net_sell');

        $sellRule = $this->createRule('Custom', 'net_sell', 'Net Sell')
            ->setMainError('\'{{niceName}}\' cannot be lower than \'Net Cost\'')
            ->setInputError('This must be higher than Net Cost');

        $this->addRule($sellRule->setValidationMethod(
            function ($value, $ruleInstance) use ($netCost) {

                if ((float) $value < (float) $netCost) {
                    return $ruleInstance->createError();
                }

                return true;
            }
        ));

        /** @var \SuttonBaker\Impresario\Model\Db\Project $modelInstance */
        $modelInstance = $this->getApp()->getRegistry()->get('model_instance');

        if ($this->getValue('status') == Project::STATUS_COMPLETE) {


            $this->addRule(
                $this->createRule('Custom', 'status', 'Status')
                    ->setMainError('This Project can\'t be marked as complete as there is still an amount remaining to be invoiced')
                    ->setInputError('This can\' be marked as complete yet')
                    ->setValidationMethod(function ($value, $ruleInstance) use ($modelInstance) {
                        if ($modelInstance->getInvoiceAmountRemaining() > 0) {
                            return $ruleInstance->createError();
                        }

                        return true;
                    })
            );

            // Add checklist items, only if the project isn't already complete (otherwise these will have to be set when re-saving projects)
            if (!$modelInstance->isComplete()) {
                foreach ($checklistItems as $checkListKey => $checklistItem) {
                    $this->addRule(
                        $this->createRule('Required', $checkListKey, $checklistItem)
                    );
                }

                // A project should always have an ID, so we can check completion files exist without having to monkey about with temporary IDs.
                if ($modelInstance->getId()) {
                    $this->addRule(
                        $this->createRule('Custom', 'status', 'Status')
                            ->setMainError('This Project can\'t be marked as complete until a completion certificate has been added')
                            ->setInputError('This can\' be marked as complete yet')
                            ->setValidationMethod(function ($value, $ruleInstance) use ($modelInstance) {
                                $completionCerts = $this->getUploadHelper()->getUploadCollection(
                                    Upload::TYPE_PROJECT_COMPLETION_CERTIFICATE,
                                    $modelInstance->getId()
                                );

                                if (count($completionCerts->load()) <= 0) {
                                    return $ruleInstance->createError();
                                }

                                return true;
                            })
                    );
                }
            }
        }
    }
}
