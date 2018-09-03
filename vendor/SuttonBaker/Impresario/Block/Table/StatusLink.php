<?php

namespace SuttonBaker\Impresario\Block\Table;
/**
 * Class StatusLink
 * @package SuttonBaker\Impresario\Block\Table
 */
class StatusLink
    extends \DaveBaker\Core\Block\Html\Table
{
    /** @var mixed */
    protected $linkCallback;

    protected function init()
    {
        parent::init();
        $this->setTemplate('html/table/link-status.phtml');
        $this->addTagIdentifier('table-status-rows');
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