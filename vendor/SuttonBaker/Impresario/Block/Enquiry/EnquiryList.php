<?php

namespace SuttonBaker\Impresario\Block\Enquiry;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;
/**
 * Class EnquiryList
 * @package SuttonBaker\Impresario\Block\Enquiry
 */
class EnquiryList
    extends \SuttonBaker\Impresario\Block\ListBase
    implements \DaveBaker\Core\Block\BlockInterface
{
    const ID_PARAM = 'enquiry_id';
    const BLOCK_PREFIX = 'enquiry';

    /** @var \SuttonBaker\Impresario\Model\Db\Enquiry\Collection $instanceCollection */
    protected $instanceCollection;

    protected function _preDispatch()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Enquiry\Collection $enquiryCollection */
        $this->instanceCollection = $this->getEnquiryHelper()->getEnquiryCollection()
            ->addOutputProcessors([
                'date_received' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'target_date' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'status' => $this->getEnquiryHelper()->getStatusOutputProcessor(),
                'edit_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getLinkHtml']),
                'delete_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getDeleteBlockHtml'])
            ]);
    }

    /**
     * @return \SuttonBaker\Impresario\Block\ListBase|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preRender()
    {
        $tableHeaders = \SuttonBaker\Impresario\Definition\Enquiry::TABLE_HEADERS;

        $this->addChildBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Template',
                "{$this->getBlockPrefix()}.list.action.bar"
            )->setTemplate('enquiry/list/action_bar.phtml')
        );

        $this->addChildBlock($this->getMessagesBlock());

        $this->addChildBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Table',
                "{$this->getBlockPrefix()}.list.table'"
            )->setHeaders(EnquiryDefinition::TABLE_HEADERS)
                ->setRecords($this->instanceCollection->load())
                ->addEscapeExcludes(['edit_column', 'delete_column'])
        );
    }

    /**
     * @return string
     */
    protected function getInstanceIdParam()
    {
        return self::ID_PARAM;
    }

    /**
     * @return string
     */
    protected function getEditPageIdentifier()
    {
        return PageDefinition::ENQUIRY_EDIT;
    }

    /**
     * @return string
     */
    protected function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }

}
