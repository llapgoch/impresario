<?php

namespace SuttonBaker\Impresario\Block\Quote;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Quote as QuoteDefinition;
/**
 * Class QuoteList
 * @package SuttonBaker\Impresario\Block\Quote
 */
class QuoteList
    extends \SuttonBaker\Impresario\Block\ListBase
    implements \DaveBaker\Core\Block\BlockInterface
{
    const BLOCK_PREFIX = 'quote';
    const COMPLETED_KEY = 'completed';
    const ID_PARAM = 'quote_id';

    /**
     * @return \SuttonBaker\Impresario\Block\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     */
    protected function _preDispatch()
    {
        $this->addChildBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Heading',
                "quote.list.heading")
                ->setHeading("Quotes")
                ->setTemplate('core/main-header.phtml')
        );

        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $enquiryCollection */
        $instanceItems = $this->getQuoteHelper()->getDisplayQuotes()
            ->addOutputProcessors([
                'date_received' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'target_date' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'status' => $this->getQuoteHelper()->getStatusOutputProcessor(),
                'edit_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getEditLinkHtml']),
                'delete_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getDeleteBlockHtml'])
            ]);

        $this->addChildBlock($this->getMessagesBlock());

        $this->addChildBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Table',
                "{$this->getBlockPrefix()}.list.table"
            )->setHeaders(QuoteDefinition::TABLE_HEADERS)->setRecords($instanceItems->load())->addEscapeExcludes(
                ['edit_column', 'delete_column']
            )
        );
    }

    /**
     * @return string
     */
    protected function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
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
        return PageDefinition::QUOTE_EDIT;
    }

}
