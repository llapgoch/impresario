<?php

namespace SuttonBaker\Impresario\Block\Table;
use DaveBaker\Core\Block\Components\Paginator;
use SuttonBaker\Impresario\Session\TableUpdater;

/**
 * Class StatusLink
 * @package SuttonBaker\Impresario\Block\Table
 */
class StatusLink
    extends Base
{
    /** @var mixed */
    protected $linkCallback;
    /** @var bool */
    protected $newWindowLink = false;
    /** @var array  */
    protected $sessionKeyItems = [];
    /** @var TableUpdater */
    protected $session;
    /** @var $rowClassCallback object */
    protected $rowClassCallback;

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _construct()
    {
        $this->addTagIdentifier('table-status-rows');
        parent::_construct();
    }

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

    protected function _preRender()
    {
        $this->unpackSession();
        parent::_preRender();
    }

    public function setRowClassCallback($callback)
    {
        $this->rowClassCallback = $callback;
        return $this;
    }

    /**
     * @param $item
     * @return $this
     */
    public function addSessionKeyItem($item)
    {
        if(!in_array($item, $this->sessionKeyItems)) {
            $this->sessionKeyItems[] = $item;
        }
        return $this;
    }

    /**
     * @param $val
     * @return $this
     */
    public function setNewWindowLink($val)
    {
        $this->newWindowLink = (bool) $val;
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
        $paginator->addLocalEvent('set_page', function(){
            $this->updateSession();
        });

        $this->unpackSessionPaginatorValues();

        return $this;
    }

    /**
     * @return Base|void
     */
    public function init()
    {
        parent::init();
        $this->setTemplate('html/table/status-link.phtml');
    }

    /**
     * @param \DaveBaker\Core\Model\Db\BaseInterface $record
     * @return mixed|string
     */
    public function getRowClass(
        \DaveBaker\Core\Model\Db\BaseInterface $record
    ) {
        if($this->rowClassCallback){
           return call_user_func_array($this->rowClassCallback, [$record]);
        }
        $statusClasses = $this->getRowStatusClasses();
        $statusKey = $this->getStatusKey();


        if($statusKey && $statusClasses){
            $statusValue = $record->getData($this->getStatusKey());
            $vals = $record->getData();
            if($statusValue && isset($statusClasses[$statusValue])){
                return $statusClasses[$statusValue];
            }
        }

        return '';
    }

    /**
     * @param \DaveBaker\Core\Model\Db\BaseInterface $record
     * @return mixed|string
     */
    public function getLink(
        $headerKey,
        \DaveBaker\Core\Model\Db\BaseInterface $record
    ) {
        if($this->linkCallback){
           return call_user_func_array($this->linkCallback, [$headerKey, $record]);
        }

        return '';
    }

    /**
     * @param $linkCallback
     * @return $this
     */
    public function setLinkCallback($linkCallback)
    {
        $this->linkCallback = $linkCallback;
        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function setStatusKey($key)
    {
        $this->setData('status_key', $key);
        return $this;
    }

    /**
     * @return string
     */
    public function getAnchorAttrs()
    {
        if($this->newWindowLink){
            return "target='_blank'";
        }

        return '';
    }

    /**
     * @return array|mixed|null
     */
    public function getStatusKey()
    {
        return $this->getData('status_key');
    }

    /**
     * @param $rowClasses
     * @return $this
     */
    public function setRowStatusClasses($rowClasses)
    {
        $this->setData('row_status_classes', $rowClasses);
        return $this;
    }

    /**
     * @return array|mixed|null
     */
    public function getRowStatusClasses()
    {
        return $this->getData('row_status_classes');
    }

    /**
     * @return mixed|TableUpdater
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getSession()
    {
        if(!$this->session){
            $this->session = $this->createAppObject(TableUpdater::class);
        }

        return $this->session;
    }

    /**
     * @param $column
     * @param string $dir
     * @return $this|\DaveBaker\Core\Block\Html\Table|Base
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function setColumnOrder($column, $dir = 'ASC')
    {
        parent::setColumnOrder($column, $dir);
        $this->updateSession();
        return $this;
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

        if($this->paginator){
            $data['pageNumber'] = $this->paginator->getPage();
        }

        $this->getSession()->set(
            $this->getSession()->createKey($this->sessionKeyItems), $data
        );

        return $this;
    }

    /**
     * @return array
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getSessionData()
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
    protected function unpackSession()
    {
        $data = $this->getSessionData();

        // Check the column still exists before setting it!
        if (is_array($data) && isset($data['orderColumn']) && $data['orderColumn']) {
            if (in_array($data['orderColumn'], array_keys($this->getCollection()->getSchema()))){
                $this->orderColumn = $data['orderColumn'];
            
                if (isset($data['orderDir'])){
                    $this->orderDir = $data['orderDir'];
                }
            }

            $this->setColumnOrder($this->orderColumn, $this->orderDir);
            $this->unpackSessionPaginatorValues();
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

        if(is_array($data)){
            if (isset($data['pageNumber']) && $this->paginator){
                $this->paginator->setPage($data['pageNumber']);
            }
        }

        return $this;
    }

}