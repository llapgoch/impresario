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
          'link' => $this->getPageUrl(PageDefintion::CLIENT_LIST)
        ];

        $navItems[] = [
            'name' => 'Create Client',
            'link' => $this->getPageUrl(PageDefintion::CLIENT_EDIT)
        ];

        $navItems[] = [
            'name' => 'Enquiries',
            'link' => $this->getPageUrl(PageDefintion::ENQUIRY_LIST)
        ];

        $navItems[] = [
            'name' => 'Create Enquiry',
            'link' => $this->getPageUrl(PageDefintion::ENQUIRY_EDIT)
        ];

        $navItems[] = [
            'name' => 'Quotes',
            'link' => $this->getPageUrl(PageDefintion::QUOTE_LIST)
        ];

        $navItems[] = [
            'name' => 'Tasks',
            'link' => $this->getPageUrl(PageDefintion::TASK_LIST)
        ];

        return $navItems;
    }
}