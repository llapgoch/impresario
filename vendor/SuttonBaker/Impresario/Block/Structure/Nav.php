<?php

namespace SuttonBaker\Impresario\Block\Structure;

use SuttonBaker\Impresario\Definition\Archive;
use SuttonBaker\Impresario\Definition\Client;
use SuttonBaker\Impresario\Definition\Supplier;
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
     * @return \DaveBaker\Core\Block\Template|void
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();

        wp_register_script('db-base-widget', get_template_directory_uri() . '/assets/js/db/db.base.widget.js', ['jquery-ui-widget']);
        wp_register_script('db-toggler-widget', get_template_directory_uri() . '/assets/js/db/db.toggler.widget.js', ['db-base-widget']);

        wp_enqueue_script('db-toggler-widget');
    }

    /**
     * @return string
     */
    public function getLogo()
    {
        return get_stylesheet_directory_uri() . '/assets/images/logo.svg';
    }

    /**
     * @return array
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function getNavItems()
    {
        $navItems = [];

        /** @var \SuttonBaker\Impresario\Helper\Client $clientHelper */
        $clientHelper = $this->createAppObject(\SuttonBaker\Impresario\Helper\Client::class);
        /** @var \SuttonBaker\Impresario\Helper\Supplier $supplierHelper */
        $supplierHelper = $this->createAppObject(\SuttonBaker\Impresario\Helper\Supplier::class);
        /** @var \SuttonBaker\Impresario\Helper\Enquiry $enquiryHelper */
        $enquiryHelper = $this->createAppObject(\SuttonBaker\Impresario\Helper\Enquiry::class);
        /** @var \SuttonBaker\Impresario\Helper\Quote $quoteHelper */
        $quoteHelper = $this->createAppObject(\SuttonBaker\Impresario\Helper\Quote::class);
        /** @var \SuttonBaker\Impresario\Helper\Task $taskHelper */
        $taskHelper = $this->createAppObject(\SuttonBaker\Impresario\Helper\Task::class);
        /** @var \SuttonBaker\Impresario\Helper\Project $projectHelper */
        $projectHelper = $this->createAppObject(\SuttonBaker\Impresario\Helper\Project::class);
        $userHelper = $this->getUserHelper();


        $navItems[] = [
            'identifier' => 'dashboard',
            'name' => 'Dashboard',
            'link' => '/',
            'icon' => 'fa-tachometer'
        ];

        if ($userHelper->hasCapability('edit_user', false)) {
            $navItems[] = [
                'identifier' => 'users',
                'name' => 'Users',
                'link' => admin_url('users.php'),
                'icon' => 'fa-user-circle-o'
            ];
        }

        if ($clientHelper->currentUserCanView()) {
            $clientNavItem = [
                'identifier' => 'clients',
                'name' => 'Clients',
                'link' => $this->getPageUrl(PageDefintion::CLIENT_LIST),
                'icon' => Client::ICON,
                'subs' => []
            ];

            $clientNavItem['subs'][] = [
                'name' => 'View Clients',
                'icon' => 'fa-eye',
                'link' => $this->getPageUrl(PageDefintion::CLIENT_LIST)
            ];

            if ($clientHelper->currentUserCanEdit()) {
                $clientNavItem['subs'][] = [
                    'name' => 'Add Client',
                    'icon' => 'fa-plus',
                    'link' => $this->getPageUrl(PageDefintion::CLIENT_EDIT)
                ];
            }

            $navItems[] = $clientNavItem;
        }

        if ($supplierHelper->currentUserCanView()) {
            $supplierNavItem = [
                'identifier' => 'suppliers',
                'name' => 'Suppliers',
                'link' => $this->getPageUrl(PageDefintion::SUPPLIER_LIST),
                'icon' => Supplier::ICON,
                'subs' => []
            ];

            $supplierNavItem['subs'][] = [
                'name' => 'View Suppliers',
                'icon' => 'fa-eye',
                'link' => $this->getPageUrl(PageDefintion::SUPPLIER_LIST)
            ];

            if ($clientHelper->currentUserCanEdit()) {
                $supplierNavItem['subs'][] = [
                    'name' => 'Add Supplier',
                    'icon' => 'fa-plus',
                    'link' => $this->getPageUrl(PageDefintion::SUPPLIER_EDIT)
                ];
            }

            $navItems[] = $supplierNavItem;
        }

        if ($enquiryHelper->currentUserCanView()) {
            $enquiryNavItem = [
                'identifier' => 'enquiries',
                'name' => 'Enquiries',
                'link' => $this->getPageUrl(PageDefintion::ENQUIRY_LIST),
                'icon' => Enquiry::ICON,
                'badge' => count($enquiryHelper->getOpenEnquiries()->load()),
                'subs' => []
            ];

            $enquiryNavItem['subs'][] = [
                'name' => 'View Enquiries',
                'icon' => 'fa-eye',
                'link' => $this->getPageUrl(PageDefintion::ENQUIRY_LIST)
            ];

            if ($enquiryHelper->currentUserCanEdit()) {
                $enquiryNavItem['subs'][] = [
                    'name' => 'Create Enquiry',
                    'icon' => 'fa-plus',
                    'link' => $this->getPageUrl(PageDefintion::ENQUIRY_EDIT)
                ];
            }

            $navItems[] = $enquiryNavItem;
        }

        if ($userHelper->hasCapability($quoteHelper->getViewCapabilities())) {
            $navItems[] = [
                'identifier' => 'quotes',
                'name' => 'Quotes',
                'link' => $this->getPageUrl(PageDefintion::QUOTE_LIST),
                'icon' => Quote::ICON,
                'badge' => count($quoteHelper->getOpenQuotes()->load())
            ];
        }

        if ($userHelper->hasCapability($taskHelper->getViewCapabilities())) {
            $navItems[] = [
                'identifier' => 'tasks',
                'name' => 'Tasks',
                'link' => $this->getPageUrl(PageDefintion::TASK_LIST),
                'icon' => Task::ICON,
                // 'badge' => count($taskHelper->getOpenTasks()->load())
            ];
        }

        if ($userHelper->hasCapability($projectHelper->getViewCapabilities())) {
            $navItems[] = [
                'identifier' => 'projects',
                'name' => 'Projects',
                'link' => $this->getPageUrl(PageDefintion::PROJECT_LIST),
                'icon' => Project::ICON,
                'badge' => count($projectHelper->getOpenProjects()->load())
            ];
        }

        if ($userHelper->hasCapability($projectHelper->getArchiveViewCapabilities())) {
            $navItems[] = [
                'identifier' => 'archives',
                'name' => 'Archive',
                'link' => $this->getPageUrl(PageDefintion::ARCHIVE_LIST),
                'icon' => Archive::ICON
            ];
        }

        $navItems[] = [
            'identifier' => 'logout',
            'name' => 'Logout',
            'icon' => 'fa fa-power-off',
            'link' => wp_logout_url()
        ];


        return $navItems;
    }
}
