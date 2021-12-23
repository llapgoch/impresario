<?php

namespace SuttonBaker\Impresario\Block\Form;
/**
 * Class Base
 * @package SuttonBaker\Impresario\Block
 */
abstract class Base extends \DaveBaker\Form\Block\Form
{
    /** @var string  */
    protected $blockPrefix = '';

    /**
     * @return \DaveBaker\Form\Block\Form|void
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();

        wp_register_script(
            'impresario_form_validator',
            get_template_directory_uri() . '/assets/js/form.validator.widget.js',
            ['jquery', 'jquery-ui-widget']
        );

        wp_enqueue_script('impresario_deleter');
    }

  /**
     * @return \DaveBaker\Core\Block\BaseInterface
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function addRecordMonitorBlock(
        \DaveBaker\Core\Model\Db\BaseInterface $model,
        $endpoint
    ) {
        if(!$model->getId()){
            return;
        }

        wp_enqueue_script('impresario_record_monitor');
 
        /** @var \DaveBaker\Form\Block\Error\Main $errorBlock */
        $this->addChildBlock(
            $this->getApp()->getBlockManager()->createBlock(
                '\DaveBaker\Core\Block\Template',
                "{$this->blockPrefix}.edit.form.record.monitor"
            )->setTemplate('components/record-monitor.phtml')
             ->setWidgetData([
                'id' => $model->getId(),
                'timestamp' => strtotime($model->getUpdatedAt()),
                'endpoint' => $endpoint
            ])
        );
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function createFormErrorBlock()
    {
        /** @var \DaveBaker\Form\Block\Error\Main $errorBlock */
        return $this->getApp()->getBlockManager()->createBlock(
            '\DaveBaker\Form\Block\Error\Main',
            "{$this->blockPrefix}.edit.form.errors"
        )->setIsReplacerBlock(true);
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
     * @return \SuttonBaker\Impresario\Helper\Supplier
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getSupplierHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Supplier');
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
     * @return \SuttonBaker\Impresario\Helper\Cost
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getCostHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Cost');
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
