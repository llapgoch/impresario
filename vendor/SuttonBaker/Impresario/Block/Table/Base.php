<?php

namespace SuttonBaker\Impresario\Block\Table;
/**
 * Class StatusLink
 * @package SuttonBaker\Impresario\Block\Table
 */
abstract class Base
    extends \DaveBaker\Core\Block\Html\Table\Collection
{
    const SORTABLE_HEADER_CLASS = 'sortable sort-alpha sort-asc';
    const SORTABLE_COLUMNS_DATA_KEY = 'sortable_columns';

    /** @var array  */
    protected $sortableColumns = [];

    /**
     * @return \DaveBaker\Core\Block\Html\Table
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addClass('table-sortable');
    }

    /**
     * @param $header
     * @return bool
     */
    public function getThClass($header)
    {
        return in_array($header, $this->getSortableColumns()) ? self::SORTABLE_HEADER_CLASS : '';
    }

    /**
     * @param $columns
     * @return $this
     */
    public function addSortableColumns($columns)
    {
        if(!is_array($columns)){
            $columns = [$columns];
        }

        $this->setData(self::SORTABLE_COLUMNS_DATA_KEY,
            array_merge_recursive($columns, $this->getSortableColumns())
        );

        return $this;
    }

    /**
     * @return array|mixed|null
     */
    public function getSortableColumns()
    {
        if(!$this->getData(self::SORTABLE_COLUMNS_DATA_KEY)){
            $this->setData(self::SORTABLE_COLUMNS_DATA_KEY, []);
        }

        return $this->getData(self::SORTABLE_COLUMNS_DATA_KEY);
    }
}