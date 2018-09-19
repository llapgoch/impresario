<?php

namespace SuttonBaker\Impresario\Block\Form;
/**
 * Class Base
 * @package SuttonBaker\Impresario\Block
 */
abstract class Base extends \DaveBaker\Form\Block\Form
{

    /**
     * @return \DaveBaker\Form\Block\Form|void
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();

        wp_register_script(
            'impresariodeleter',
            get_template_directory_uri() . '/assets/js/deleter.widget.js',
            ['jquery', 'jquery-ui-widget']
        );
        wp_enqueue_script('impresariodeleter');
    }

    /**
     * @param $label
     * @param $url
     * @param $blockName
     * @param null $asName
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function createSmallButtonElement($label, $url, $blockName, $asName = null)
    {
        return $this->createBlock(
            '\DaveBaker\Core\Block\Html\Tag',
            $blockName,
            $asName
        )->setTagText($label)
            ->setTag('a')
            ->addAttribute(['href' => $url])
            ->addClass('btn btn-sm btn-primary');
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
     * @return \SuttonBaker\Impresario\Helper\Client
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
     * @return \DaveBaker\Form\SelectConnector\Collection
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createCollectionSelectConnector()
    {
        return $this->createAppObject('\DaveBaker\Form\SelectConnector\Collection');
    }

    /**
     * @return \DaveBaker\Form\SelectConnector\AssociativeArray
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createArraySelectConnector()
    {
        return $this->createAppObject('\DaveBaker\Form\SelectConnector\AssociativeArray');
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

    /**
     * @return \SuttonBaker\Impresario\Helper\Role
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getRoleHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Role');
    }

}
