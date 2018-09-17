<?php

namespace SuttonBaker\Impresario\Block\Table;
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
     * @param $val
     * @return $this
     */
    public function setNewWindowLink($val)
    {
        $this->newWindowLink = (bool) $val;
        return $this;
    }

    /**
     * @return Base|void
     */
    public function init()
    {
        parent::init();
        $this->setTemplate('html/table/link-status.phtml');
    }

    /**
     * @param \DaveBaker\Core\Model\Db\BaseInterface $record
     * @return mixed|string
     */
    public function getRowClass(
        \DaveBaker\Core\Model\Db\BaseInterface $record
    ) {
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
}