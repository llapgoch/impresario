<?php

namespace SuttonBaker\Impresario\Block\Table;

use SuttonBaker\Impresario\Session\TableUpdater;
use SuttonBaker\Impresario\Definition\Filter as FilterDefinition;

/**
 * Class StatusLink
 * @package SuttonBaker\Impresario\Block\Table
 */
abstract class Base
extends \DaveBaker\Core\Block\Html\Table\Collection
{
    /** @var TableUpdater */
    protected $session;
    /** @var array  */
    public $sessionKeyItems = [];
    /** @var array */
    protected $filters = [];
    /** @var array */
    protected $filterSchema = [];
    /** @var bool */
    protected $showEmptyTable;

    /**
     * @return Base|void
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $this->addSessionKeyItem($this->getName());
        $this->addSessionKeyItem($this->getUrlHelper()->getCurrentUrl());
        
        parent::_preDispatch();
    }


    /**
     * @return array
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getSessionData()
    {
        return $this->getSession()->get(
            $this->getSession()->createKey($this->sessionKeyItems)
        );
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function unpackSession()
    {
        $data = $this->getSessionData();
        
        if(isset($data['filters'])){
            $this->setFilters($data['filters'], false);
        }
        
        // Check the column still exists before setting it!
        if (is_array($data) && isset($data['orderColumn']) && $data['orderColumn']) {
            $this->orderColumn = $data['orderColumn'];

            if (isset($data['orderDir'])) {
                $this->orderDir = $data['orderDir'];
            }

            $this->setColumnOrder($this->orderColumn, $this->orderDir, false);
        }

        return $this;
    }

    /**
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @return $this
     */
    protected function unpackSessionPaginatorValues()
    {
        $data = $this->getSessionData();
        
        if (is_array($data)) {
            if (isset($data['pageNumber']) && $this->paginator) {
                $this->paginator->setPage($data['pageNumber']);
            }
        }

        return $this;
    }

    /**
     * @param Paginator $paginator
     * @return $this|Base
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function setPaginator($paginator)
    {
        parent::setPaginator($paginator);

        // Add a local event to the paginator to update the session data when the page is changed
        $paginator->addLocalEvent('set_page', function () {
            $this->updateSession();
        });

        // $this->unpackSessionPaginatorValues();

        return $this;
    }


    /**
     * @param $column
     * @param string $dir
     * @return $this|\DaveBaker\Core\Block\Html\Table|Base
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function setColumnOrder($column, $dir = 'ASC', $updateSession = true)
    {
        parent::setColumnOrder($column, $dir);

        if($updateSession){
            $this->updateSession();
        }
        return $this;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setShowEmptyTable($value)
    {
        $this->showEmptyTable = (bool) $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function getShowEmptyTable()
    {
        return $this->showEmptyTable;
    }

    /**
     * @param $item
     * @return $this
     */
    public function addSessionKeyItem($item)
    {
        if (!in_array($item, $this->sessionKeyItems)) {
            $this->sessionKeyItems[] = $item;
        }
        return $this;
    }

    /**
     * @return mixed|TableUpdater
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getSession()
    {
        if (!$this->session) {
            $this->session = $this->createAppObject(TableUpdater::class);
        }

        return $this->session;
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function updateSession()
    {
        
        $data = [
            'orderColumn' => $this->orderColumn,
            'orderDir' => $this->orderDir
        ];
        
        if ($this->paginator) {
            $data['pageNumber'] = $this->paginator->getPage();
        }

        $data['filters'] = $this->getFilters();

        $this->getSession()->set(
            $this->getSession()->createKey($this->sessionKeyItems),
            $data
        );
        

        return $this;
    }


    protected function _preRender()
    {           
        $this->unpackSessionPaginatorValues();
        parent::_preRender();
    }

    /**
     * @param array $filters
     * @return $this
     */
    public function setFilters(
        $filters,
        $updateSession = true
    ) {
        $this->filters = $filters;

        // $this->getCollection()->getSelect()->reset(\Zend_Db_Select::WHERE);
        
        foreach ($this->filters as $name => $item) {
            $this->applyFilter($name, $item);
        }

        if($updateSession){
            $this->updateSession();
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     *
     * @param string $name
     * @param array $filter
     * @return $this
     */
    protected function applyFilter(
        $name,
        $filter
    ) {
        if (!$this->filterSchema) {
            throw new \Exception("No Filter schema set");
        }

        if (!isset($this->filterSchema[$name])) {
            throw new \Exception("Filter $name not found in schema");
        }

        $schema = $this->filterSchema[$name];

        $collection = $this->getCollection();
        $compareType = FilterDefinition::COMPARE_TYPE_EQ;
        $column = $name;
        $fieldType = FilterDefinition::FIELD_TYPE_TEXT;
        $dataConverterClass = null;
        $dataConverterMethod = null;

        if (isset($schema[FilterDefinition::DATA_CONVERTER])) {
            if (
                !isset($schema[FilterDefinition::DATA_CONVERTER][FilterDefinition::DATA_CONVERTER_CLASS])
                || !isset($schema[FilterDefinition::DATA_CONVERTER][FilterDefinition::DATA_CONVERTER_METHOD])
            ) {
                throw new \Exception("Class or method not defined for data converter");
            }

            $dataConverterClass = $schema[FilterDefinition::DATA_CONVERTER][FilterDefinition::DATA_CONVERTER_CLASS];
            $dataConverterMethod = $schema[FilterDefinition::DATA_CONVERTER][FilterDefinition::DATA_CONVERTER_METHOD];
        }

        if (isset($schema[FilterDefinition::COMPARE_TYPE])) {
            $compareType = $schema[FilterDefinition::COMPARE_TYPE];
        }

        if (isset($schema[FilterDefinition::MAP])) {
            $column = $schema[FilterDefinition::MAP];
        }

        if (isset($schema[FilterDefinition::FIELD_TYPE])) {
            $fieldType = $schema[FilterDefinition::FIELD_TYPE];
        }

        if ($filter) {
            if ($fieldType == FilterDefinition::FIELD_TYPE_RANGE) {
                if (
                    isset($filter[FilterDefinition::RANGE_LOW]) &&
                    $filter[FilterDefinition::RANGE_LOW] !== ""
                ) {
                    $value = $this->applyDataConverter(
                        $dataConverterClass,
                        $dataConverterMethod,
                        $filter[FilterDefinition::RANGE_LOW]
                    );

                    $collection->where("$column >= ?", $value);
                }

                if (
                    isset($filter[FilterDefinition::RANGE_HIGH])
                    && $filter[FilterDefinition::RANGE_HIGH] !== ""
                ) {
                    $value = $this->applyDataConverter(
                        $dataConverterClass,
                        $dataConverterMethod,
                        $filter[FilterDefinition::RANGE_HIGH]
                    );

                    $collection->where("$column <= ?", $value);
                }
            } else {
                $filter = $this->applyDataConverter(
                    $dataConverterClass,
                    $dataConverterMethod,
                    $filter
                );

                if ($compareType == FilterDefinition::COMPARE_TYPE_LIKE) {
                    $filter = "%$filter%";
                }
                $collection->where("$column $compareType ?", $filter);
            }
        }

        return $this;
    }

    /**
     * Converts a value from client side to a format for the DB
     *
     * @param string $converterClass
     * @param string $converterMethod
     * @param mixed $value
     * @return mixed
     */
    protected function applyDataConverter(
        $converterClass,
        $converterMethod,
        $value
    ) {
        if (!$converterClass || !$converterMethod) {
            return $value;
        }

        $converter = $this->createAppObject($converterClass);
        return $converter->{$converterMethod}($value);
    }

    /**
     * @param array $filterSchema
     * @return $this
     */
    public function setFilterSchema(
        $filterSchema
    ) {
        $this->filterSchema = $filterSchema;
        return $this;
    }
}
