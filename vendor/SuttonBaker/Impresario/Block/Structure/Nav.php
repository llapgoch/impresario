<?php
namespace SuttonBaker\Impresario\Block\Structure;

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

        $userHelper = $this->getUserHelper();

        if($userHelper->hasCapability([Roles::CAP_ALL, Roles::CAP_EDIT_CLIENT, Roles::CAP_VIEW_CLIENT])) {
            $navItems[] = [
                'name' => 'Clients',
                'link' => $this->getPageUrl(PageDefintion::CLIENT_LIST),
                'icon' => Client::ICON
            ];
        }

        if($userHelper->hasCapability([Roles::CAP_ALL, Roles::CAP_EDIT_CLIENT])) {
            $navItems[] = [
                'name' => 'Create Client',
                'link' => $this->getPageUrl(PageDefintion::CLIENT_EDIT),
                'icon' => 'fa-plus'
            ];
        }

        if($userHelper->hasCapability([Roles::CAP_ALL, Roles::CAP_EDIT_ENQUIRY, Roles::CAP_VIEW_ENQUIRY])) {
            $navItems[] = [
                'name' => 'Enquiries',
                'link' => $this->getPageUrl(PageDefintion::ENQUIRY_LIST),
                'icon' => Enquiry::ICON
            ];
        }

        if($userHelper->hasCapability([Roles::CAP_ALL, Roles::CAP_EDIT_ENQUIRY])) {
            $navItems[] = [
                'name' => 'Create Enquiry',
                'link' => $this->getPageUrl(PageDefintion::ENQUIRY_EDIT),
                'icon' => 'fa-plus'
            ];
        }

        if($userHelper->hasCapability([Roles::CAP_ALL, Roles::CAP_EDIT_QUOTE, Roles::CAP_VIEW_QUOTE])) {
            $navItems[] = [
                'name' => 'Quotes',
                'link' => $this->getPageUrl(PageDefintion::QUOTE_LIST),
                'icon' => Quote::ICON
            ];
        }

        if($userHelper->hasCapability([Roles::CAP_ALL, Roles::CAP_VIEW_TASK, Roles::CAP_EDIT_TASK])) {
            $navItems[] = [
                'name' => 'Tasks',
                'link' => $this->getPageUrl(PageDefintion::TASK_LIST),
                'icon' => Task::ICON
            ];
        }

        if($userHelper->hasCapability([Roles::CAP_ALL, Roles::CAP_VIEW_PROJECT, Roles::CAP_EDIT_PROJECT])) {
            $navItems[] = [
                'name' => 'Projects',
                'link' => $this->getPageUrl(PageDefintion::PROJECT_LIST),
                'icon' => Project::ICON
            ];
        }


        return $navItems;
    }
}