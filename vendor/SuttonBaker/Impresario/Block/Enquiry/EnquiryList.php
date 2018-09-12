<?php

namespace SuttonBaker\Impresario\Block\Enquiry;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Enquiry as EnquiryDefinition;
use \DaveBaker\Core\Definitions\Table as TableDefinition;
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

    /**
     * @return \SuttonBaker\Impresario\Block\ListBase|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function _preDispatch()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Enquiry\Collection $enquiryCollection */
        $this->instanceCollection = $this->getEnquiryHelper()->getEnquiryCollection()
            ->addOutputProcessors([
                'date_received' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'target_date' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'status' => $this->getEnquiryHelper()->getStatusOutputProcessor(),
                'edit_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getEditLinkHtml'])
            ]);

        $tableHeaders = EnquiryDefinition::TABLE_HEADERS;

        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $enquiryCollection */
        $instanceItems = $this->getEnquiryHelper()->getEnquiryCollection()
            ->addOutputProcessors([
                'date_received' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'target_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'status' => $this->getEnquiryHelper()->getStatusOutputProcessor()
            ]);

        // Do this check, as we won't have the maintile when reloading the table with ajax
        $mainTile = $this->getBlockManager()->getBlock('enquiry.tile.main');
        $mainTile->addChildBlock(
        /** @var Paginator $paginator */
            $paginator = $this->createBlock(
                '\DaveBaker\Core\Block\Components\Paginator',
                'enquiry.list.paginator',
                'footer'
            )->setOrder('after', 'enquiry.list.table')
                ->setRecordsPerPage(EnquiryDefinition::RECORDS_PER_PAGE)
                ->setTotalRecords(count($instanceItems->getItems()))
                ->setIsReplacerBlock(true)
        );

        $this->addChildBlock(
            $tableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Table\StatusLink',
                'enquiry.list.table')
                ->setHeaders($tableHeaders)->setRecords($instanceItems)
                ->setSortableColumns(EnquiryDefinition::SORTABLE_COLUMNS)
                ->setStatusKey('status')
                ->setRowStatusClasses(EnquiryDefinition::getRowClasses())
                ->addJsDataItems([
                    TableDefinition::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                        $this->getUrlHelper()->getApiUrl(EnquiryDefinition::API_ENDPOINT_UPDATE_TABLE)
                ])
                ->setPaginator($paginator)
        );

        $tableBlock->setLinkCallback(
            function ($headerKey, $record) {
                return $this->getPageUrl(
                    \SuttonBaker\Impresario\Definition\Page::ENQUIRY_EDIT,
                    ['enquiry_id' => $record->getId()]
                );
            }
        );
    }

    /**
     * @return \SuttonBaker\Impresario\Block\ListBase|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function _preRender()
    {
        wp_enqueue_script('dbwpcore_table_updater');


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
