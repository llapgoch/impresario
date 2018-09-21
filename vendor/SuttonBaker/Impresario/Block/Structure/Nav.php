<?php
namespace SuttonBaker\Impresario\Block\Structure;

use SuttonBaker\Impresario\Definition\Archive;
use SuttonBaker\Impresario\Definition\Client;
use SuttonBaker\Impresario\Definition\Enquiry;
use \SuttonBaker\Impresario\Definition\Page as PageDefintion;
use SuttonBaker\Impresario\Definition\Project;
use SuttonBaker\Impresario\Definition\Quote;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Definition\Task;

/**
 * Class Nav
 * @package SuttonBaker\Impresario\Block\Structure
 */
class Nav extends \DaveBaker\Core\Block\Template
{
    /**
     * @return array
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getNavItems()
    {
        $navItems = [];

        /** @var \SuttonBaker\Impresario\Helper\Client $clientHelper */
        $clientHelper = $this->createAppObject(\SuttonBaker\Impresario\Helper\Client::class);
        /** @var \SuttonBaker\Impresario\Helper\Enquiry $enquiryHelper */
        $enquiryHelper = $this->createAppObject(\SuttonBaker\Impresario\Helper\Enquiry::class);
        /** @var \SuttonBaker\Impresario\Helper\Quote $quoteHelper */
        $quoteHelper = $this->createAppObject(\SuttonBaker\Impresario\Helper\Quote::class);
        /** @var \SuttonBaker\Impresario\Helper\Task $taskHelper */
        $taskHelper = $this->createAppObject(\SuttonBaker\Impresario\Helper\Task::class);
        /** @var \SuttonBaker\Impresario\Helper\Project $projectHelper */
        $projectHelper = $this->createAppObject(\SuttonBaker\Impresario\Helper\Project::class);
        $userHelper = $this->getUserHelper();

        if($userHelper->hasCapability($clientHelper->getViewCapabilities())) {
            $navItems[] = [
                'name' => 'Clients',
                'link' => $this->getPageUrl(PageDefintion::CLIENT_LIST),
                'icon' => Client::ICON
            ];
        }

        if($userHelper->hasCapability($clientHelper->getEditCapabilities())) {
            $navItems[] = [
                'name' => 'Create Client',
                'link' => $this->getPageUrl(PageDefintion::CLIENT_EDIT),
                'icon' => 'fa-plus'
            ];
        }

        if($userHelper->hasCapability($enquiryHelper->getViewCapabilities())) {
            $navItems[] = [
                'name' => 'Enquiries',
                'link' => $this->getPageUrl(PageDefintion::ENQUIRY_LIST),
                'icon' => Enquiry::ICON
            ];
        }

        if($userHelper->hasCapability($enquiryHelper->getEditCapabilities())) {
            $navItems[] = [
                'name' => 'Create Enquiry',
                'link' => $this->getPageUrl(PageDefintion::ENQUIRY_EDIT),
                'icon' => 'fa-plus'
            ];
        }

        if($userHelper->hasCapability($quoteHelper->getViewCapabilities())) {
            $navItems[] = [
                'name' => 'Quotes',
                'link' => $this->getPageUrl(PageDefintion::QUOTE_LIST),
                'icon' => Quote::ICON
            ];
        }

        if($userHelper->hasCapability($taskHelper->getViewCapabilities())) {
            $navItems[] = [
                'name' => 'Tasks',
                'link' => $this->getPageUrl(PageDefintion::TASK_LIST),
                'icon' => Task::ICON
            ];
        }

        if($userHelper->hasCapability($projectHelper->getViewCapabilities())) {
            $navItems[] = [
                'name' => 'Projects',
                'link' => $this->getPageUrl(PageDefintion::PROJECT_LIST),
                'icon' => Project::ICON
            ];

            $navItems[] = [
                'name' => 'Archive',
                'link' => $this->getPageUrl(PageDefintion::ARCHIVE_LIST),
                'icon' => Archive::ICON
            ];
        }


        return $navItems;
    }
}