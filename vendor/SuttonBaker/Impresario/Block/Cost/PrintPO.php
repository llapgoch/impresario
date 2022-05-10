<?php

namespace SuttonBaker\Impresario\Block\Cost;

use DaveBaker\Core\Helper\Date;
use Exception;
use SuttonBaker\Impresario\Definition\Cost as DefinitionCost;
use SuttonBaker\Impresario\Model\Db\Client;
use SuttonBaker\Impresario\Model\Db\Cost;
use SuttonBaker\Impresario\Model\Db\Project;

class PrintPO extends \DaveBaker\Core\Block\Template
{
    protected $template = 'cost-po/print.phtml';

    /** @var Cost */
    protected $cost;
    /** @var Project */
    protected $project;
    /** @var Client */
    protected $client;
    /** @var User */
    protected $projectManager;
    /** @var array */
    protected $poItems = [];

    /**
     * 
     *
     * @param Cost $cost
     * @return $this
     */
    public function setCost(Cost $cost)
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     *
     * @return Cost
     */
    public function getCost()
    {
        if (!$this->cost) {
            throw new Exception("Cost not set");
        }
        return $this->cost;
    }


    /**
     *
     * @return string
     */
    public function getCostDate()
    {
        return $this->getDateHelper()->utcDbDateToShortLocalOutput($this->getCost()->getCostDate());
    }

    /**
     *
     * @return Client
     */
    public function getClient()
    {
        if (!$this->client) {
            $this->client = $this->getClientHelper()->getClient($this->getProject()->getClientId());
        }

        return $this->client;
    }

    public function getProject()
    {
        if (!$this->project) {
            $this->project = $this->getCostHelper()->getParentForCost($this->getCost());
        }

        return $this->project;
    }

    /**
     *
     * @return string
     */
    public function getProjectManagerName()
    {
        if (!$this->projectManager) {
            $this->projectManager = $this->getUserHelper()->getUser($this->getProject()->getProjectManagerId());
        }

        return $this->projectManager->getDisplayName();
    }

    /**
     * This only applies to project types currently
     * 
     * @return array
     */
    public function getPOItems()
    {
        if (!$this->poItems) {
            $result = $this->getCostHelper()->getCostInvoiceItems($this->getCost()->getId());
            $this->poItems = $result->getItems();
        }

        return $this->poItems;
    }

    /**
     *
     * @param mixed $value
     * @return string
     */
    public function formatCurrency($value)
    {
        return $this->getLocaleHelper()->formatCurrency($value);
    }

    /**
     *
     * @return string
     */
    public function getClientName()
    {
        $client = $this->getClient();

        if (!$client->getId()) {
            return '- -';
        }

        return $client->getClientName();
    }


    /**
     *
     * @return string
     */
    public function getSiteName()
    {
        $project = $this->getProject();

        if ($siteName = $project->getSiteName()) {
            return $siteName;
        }

        return '- -';
    }


    /**
     *
     * @return string
     */
    public function getSupplierAddress()
    {
        $supplier = $this->getSupplierHelper()->getSupplier($this->getCost()->getSupplierId());

        if (!$supplier->getId()) {
            return '- -';
        }

        $addressParts = [
            $this->escapeHtml($supplier->getSupplierName()),
            $this->escapeHtml($supplier->getAddressLine1()),
            $this->escapeHtml($supplier->getAddressLine2()),
            $this->escapeHtml($supplier->getAddressLine3()),
            $this->escapeHtml($supplier->getCounty()),
            $this->escapeHtml($supplier->getPostcode()),
        ];

        return implode(",<br />", array_filter($addressParts));
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Cost
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getCostHelper()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\Cost::class);
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Supplier
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getSupplierHelper()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\Supplier::class);
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getProjectHelper()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\Project::class);
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Client
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getClientHelper()
    {
        return $this->createAppObject(\SuttonBaker\Impresario\Helper\Client::class);
    }

    /**
     *
     * @return Date
     */
    protected function getDateHelper()
    {
        return $this->getApp()->getHelper('Date');
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return get_stylesheet_directory_uri() . '/assets/images/logo.svg';
    }
}
