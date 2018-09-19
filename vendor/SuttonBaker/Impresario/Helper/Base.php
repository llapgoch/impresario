<?php

namespace SuttonBaker\Impresario\Helper;
use SuttonBaker\Impresario\Definition\Flow;

/**
 * Class Base
 * @package SuttonBaker\Impresario\Helper
 */
abstract class Base extends \DaveBaker\Core\Helper\Base
{
    /** @var array  */
    protected $editCapabilities = [];
    /** @var array  */
    protected $viewCapabilities = [];

    /**
     * @param string $for
     * @param \SuttonBaker\Impresario\Model\Db\Enquiry|null $enquiry
     * @param \SuttonBaker\Impresario\Model\Db\Quote|null $quote
     * @param \SuttonBaker\Impresario\Model\Db\Project|null $project
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getTabBar(
        $for,
        \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry = null,
        \SuttonBaker\Impresario\Model\Db\Quote $quote = null,
        \SuttonBaker\Impresario\Model\Db\Project $project = null
    ) {
        $tabs = Flow::getTabs();

        foreach($tabs as $type => $tab){
            $item = null;
            $url = false;

            if($type == $for){
                $tabs[$type]['disabled'] = true;
                $tabs[$type]['active'] = true;
                $tabs[$type]['href'] = 'javascript:;';

                continue;
            }

            switch($type){
                case 'enquiry':
                    $item = $enquiry;
                    $url = $this->getEnquiryHelper()->getUrlForEnquiry($enquiry, true);
                    break;
                case 'quote':
                    $item = $quote;
                    $url = $this->getQuoteHelper()->getUrlForQuote($quote, true);
                    break;
                case 'project':
                    $item = $project;
                    $url = $this->getProjectHelper()->getUrlForProject($project, true);
                    break;
            }

            if(!$url){
                $tabs[$type]['disabled'] = true;
                $tabs[$type]['href'] = 'javascript:;';
            }else{
                $tabs[$type]['href'] = $url;
            }
        }

        $tabBlock = $this->getApp()->getBlockManager()->createBlock(
            '\SuttonBaker\Impresario\Block\Core\Tile\Tabs',
            "$type.tile.tabs",
            'tabs'
        )->setTabs($tabs);

        return $tabBlock;
    }
    /**
     * @param $instance
     * @param $capabilities
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getActionVerb(
        \DaveBaker\Core\Model\Db\BaseInterface $instance)
    {
        if(!$this->getUserHelper()->hasCapability($this->getEditCapabilities())){
            return 'View';
        }

        if(!$instance->getId()){
            return 'Create';
        }

        return 'Update';
    }
    /**
     * @param string $key
     * @param array $items
     * @return mixed|string
     */
    protected function getDisplayName($key, $items = []){
        if(isset($items[$key])){
            return $items[$key];
        }

        return '';
    }
    /**
     * @return \SuttonBaker\Impresario\Helper\Task
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getTaskHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Task');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getEnquiryHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Enquiry');
    }

    /**
     * @return bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function currentUserCanEdit()
    {
        return $this->getUserHelper()->hasCapability($this->editCapabilities);
    }

    /**
     * @return bool
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function currentUserCanView()
    {
        return $this->getUserHelper()->hasCapability($this->viewCapabilities);
    }

    /**
     * @return array
     */
    public function getViewCapabilities()
    {
        return $this->viewCapabilities;
    }
    /**
     * @return array
     */
    public function getEditCapabilities()
    {
        return $this->editCapabilities;
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Enquiry
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getClientHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Client');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Quote
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getQuoteHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Quote');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Project
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getProjectHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Project');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Invoice
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getInvoiceHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Invoice');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Variation
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getVariationHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Variation');
    }
}