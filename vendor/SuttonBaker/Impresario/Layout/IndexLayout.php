<?php

namespace SuttonBaker\Impresario\Layout;
use DaveBaker\Core\Block\Block;
use SuttonBaker\Impresario\Definition\Page as PageDefinition;

/**
 * Class Client
 * @package SuttonBaker\Impresario\Layout
 */
class IndexLayout extends Base
{
    /** @var string  */
    protected $headingName = 'Dashboard';
    /** @var string  */
    protected $icon = 'fa-tachometer';
    /** @var string  */
    protected $headingShortcode = 'page_heading';
    /** @var Block */
    protected $rootContainer;


    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function indexHandle()
    {
        $this->addHeading()->addMessages();
    }
}