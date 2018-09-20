<?php

namespace SuttonBaker\Impresario\Model\Db;
/**
 * Class Quote
 * @package SuttonBaker\Impresario\Model\Db
 */
class Quote extends Base
{
    protected $pastRevisionsCollection;
    /**
     * @return $this
     */
    protected function init()
    {
        $this->tableName = 'quote';
        $this->idColumn = 'quote_id';

        return $this;
    }

    /**
     * @return null|Quote\Collection
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function getPastRevisions()
    {
        if(!$this->getId()){
            return null;
        }

        if(!$this->pastRevisionsCollection) {
            $revisions = $this->getQuoteHelper()->getQuoteCollection();
            $revisions->getSelect()->reset(\Zend_Db_Select::ORDER);

            $revisions->order('created_at DESC')
                ->where('parent_id=?', $this->getId())
                ->where('is_superseded=?', 1);

            $this->pastRevisionsCollection = $revisions;
        }

        return $this->pastRevisionsCollection;
    }

    /**
     * @return Quote
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getParentQuote()
    {
        return $this->getQuoteHelper()->getQuote($this->getParentId());
    }

    /**
     * @return float
     */
    public function getProfit()
    {
        if(is_numeric($this->getNetSell()) && is_numeric($this->getNetCost())){
            return $this->getNetSell() - $this->getNetCost();
        }

        return 0;
    }

    /**
     * @return float
     */
    public function getGp()
    {
        if(is_numeric($this->getNetSell()) && is_numeric($this->getNetCost())){
            return ($this->getProfit() / $this->getNetSell()) * 100;
        }

        return 0;
    }

    protected function beforeSave()
    {
        $this->setData('gp', $this->getGp());
        $this->setData('profit', $this->getProfit());
    }
}