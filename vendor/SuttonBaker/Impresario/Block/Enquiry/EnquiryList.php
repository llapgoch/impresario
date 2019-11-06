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
    /** @var \DaveBaker\Core\Block\Components\Paginator */
    protected $paginator;
    /** @var \SuttonBaker\Impresario\Block\Table\StatusLink */
    protected $tableBlock;

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
        $mainTile = $this->getBlockManager()->getBlock('enquiry.tile.main');


        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $enquiryCollection */
        $this->instanceCollection = $this->getEnquiryHelper()->getDisplayEnquiries()
            ->addOutputProcessors([
                'date_received' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'target_date' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'status' => $this->getEnquiryHelper()->getStatusOutputProcessor()
            ]);


        $mainTile->addChildBlock(
            /** @var Paginator $paginator */
            $this->paginator = $this->createBlock(
                '\DaveBaker\Core\Block\Components\Paginator',
                'enquiry.list.paginator',
                'footer'
            )->setRecordsPerPage(EnquiryDefinition::RECORDS_PER_PAGE)
            ->setIsReplacerBlock(true)
        );


        $this->addChildBlock(
            $this->tableBlock = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Table\StatusLink',
                'enquiry.list.table'
            )
                ->setHeaders($tableHeaders)->setRecords($this->instanceCollection)
                ->setSortableColumns(EnquiryDefinition::SORTABLE_COLUMNS)
                ->setStatusKey('status')
                ->setRowStatusClasses(EnquiryDefinition::getRowClasses())
                ->addJsDataItems([
                    TableDefinition::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
                    $this->getUrlHelper()->getApiUrl(EnquiryDefinition::API_ENDPOINT_UPDATE_TABLE)
                ])
                ->setPaginator($this->paginator)
                ->addClass('js-enquiry-table')
                ->setShowEmptyTable(true)
                ->setFilterSchema(
                    \SuttonBaker\Impresario\Definition\Enquiry::FILTER_LISTING
                )
        );

        /** @var \SuttonBaker\Impresario\Block\Form\Filter\Set $filterBlock */
        $filterBlock = $this->getBlockManager()->getBlock('enquiry.filter.set');
        $this->tableBlock->preDispatch();

        if (($sessionData = $this->tableBlock->getSessionData())
            && isset($sessionData['filters'])
        ) {
            foreach ($sessionData['filters'] as $filterKey => $filterValue) {
                $filterBlock->setFilterValue($filterKey, $filterValue);
            }
        }

        $this->tableBlock->unpackSession();
    }

    /**
     * Method to allow the resetting of paginator values when using the API
     *
     * @return void
     */
    public function applyRecordCountToPaginator()
    {
        $this->paginator
        ->setTotalRecords(count($this->instanceCollection->getItems()));
        return $this;
    }

    protected function _preRender()
    {
        $this->tableBlock->setRecords($this->instanceCollection);
            
        $this->applyRecordCountToPaginator();

        $hiddenClass = $this->getElementConfig()->getConfigValue('hiddenClass');

        $this->addChildBlock(
            $noItemsBlock = $this->getNoItemsBlock('enquiry.list.table.noitems')
        );
        $noItemsBlock->setIsReplacerBlock(true);

        if (!count($this->instanceCollection->getItems())) {
            $this->paginator->addClass($hiddenClass);
            $this->tableBlock->addClass($hiddenClass);
        } else {
            $noItemsBlock->addClass($hiddenClass);
        }

        $this->tableBlock->setLinkCallback(
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
