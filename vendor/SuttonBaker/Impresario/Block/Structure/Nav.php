<?php
namespace SuttonBaker\Impresario\Block\Structure;

use \SuttonBaker\Impresario\Definition\Page as PageDefintion;

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

        $navItems[] = [
            'name' => 'Clients',
            'link' => $this->getPageUrl(PageDefintion::CLIENT_LIST),
            'icon' => 'fa-address-book'
        ];

        $navItems[] = [
            'name' => 'Create Client',
            'link' => $this->getPageUrl(PageDefintion::CLIENT_EDIT),
            'icon' => 'fa-plus'
        ];

        $navItems[] = [
            'name' => 'Enquiries',
            'link' => $this->getPageUrl(PageDefintion::ENQUIRY_LIST),
            'icon' => 'fa-thumb-tack'
        ];

        $navItems[] = [
            'name' => 'Create Enquiry',
            'link' => $this->getPageUrl(PageDefintion::ENQUIRY_EDIT),
            'icon' => 'fa-plus'
        ];

        $navItems[] = [
            'name' => 'Quotes',
            'link' => $this->getPageUrl(PageDefintion::QUOTE_LIST),
            'icon' => 'fa-calculator'
        ];

        $navItems[] = [
            'name' => 'Tasks',
            'link' => $this->getPageUrl(PageDefintion::TASK_LIST),
            'icon' => 'fa-th-list'
        ];

        return $navItems;
    }
}