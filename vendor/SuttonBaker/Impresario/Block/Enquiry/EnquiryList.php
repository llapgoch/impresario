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
        wp_enqueue_script('dbwpcore_table_updater');

        $tableHeaders = EnquiryDefinition::TABLE_HEADERS;

        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $enquiryCollection */
        $this->instanceCollection = $this->getEnquiryHelper()->getDisplayEnquiries()
            ->addOutputProcessors([
                'date_received' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'target_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'status' => $this->getEnquiryHelper()->getStatusOutputProcessor()
            ]);

        $mainTile = $this->getBlockManager()->getBlock('enquiry.tile.main');
        $mainTile->addChildBlock(
        /** @var Paginator $paginator */
            $paginator = $this->createBlock(
                '\DaveBaker\Core\Block\Components\Paginator',
                'enquiry.list.paginator',
                'footer'
            )->setRecordsPerPage(EnquiryDefinition::RECORDS_PER_PAGE)
                ->setTotalRecords(count($this->instanceCollection->getItems()))
                ->setIsReplacerBlock(true)
        );

        $this->addChildBlock(
            $tableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Table\StatusLink',
                'enquiry.list.table')
                ->setHeaders($tableHeaders)->setRecords($this->instanceCollection)
                ->setSortableColumns(EnquiryDefinition::SORTABLE_COLUMNS)
                ->setStatusKey('status')
                ->setRowStatusClasses(EnquiryDefinition::getRowClasses())
                ->addJsDataItems([
                    TableDefinition::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                        $this->getUrlHelper()->getApiUrl(EnquiryDefinition::API_ENDPOINT_UPDATE_TABLE)
                ])
                ->setPaginator($paginator)
        );

        if(!count($this->instanceCollection->getItems())){
            $this->addChildBlock($this->getNoItemsBlock());
        }

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
