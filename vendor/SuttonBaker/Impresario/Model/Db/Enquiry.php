<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Model\Db
 */
class Enquiry extends Base
{
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'enquiry';
        $this->idColumn = 'enquiry_id';

        return $this;
    }

    /**
     * @return \SuttonBaker\Impresario\Model\Db\Quote
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     *
     * Gets the newest non-deleted quote entity for the enquiry
     */
    public function getQuoteEntity()
    {
        $quote = $this->getQuoteHelper()->getQuote();

        $collection = $this->getQuoteHelper()->getQuoteCollection();
        $collection->getSelect()->where('enquiry_id=?', $this->getId());
        $collection->getSelect()->columns(new \Zend_Db_Expr('MAX(quote_id) as max_quote_id'));

        if($firstItem = $collection->firstItem()){
            $quote->load($firstItem->getMaxQuoteId());
        }

        return $quote;
    }
}