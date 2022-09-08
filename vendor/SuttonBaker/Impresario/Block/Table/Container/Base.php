<?php

namespace SuttonBaker\Impresario\Block\Table\Container;
/**
 * Class Base
 * @package SuttonBaker\Impresario\Block\Table
 */
abstract class Base
    extends \SuttonBaker\Impresario\Block\Base
{
    /** @var string  */
    protected $blockPrefix = '';
    /** @var string  */
    protected $idParam = '';
    /** @var string  */
    protected $tileDefinitionClass = '\SuttonBaker\Impresario\Block\Core\Tile\Black';

    /* @var \SuttonBaker\Impresario\Helper\Listing\Utility */
    protected $listingUtility;
    /** @var array  */
    protected $editLinkParams = [];

    /**
     * @param array $editLinkParams
     * @return $this
     */
    public function setEditLinkParams($editLinkParams)
    {
        $this->editLinkParams = $editLinkParams;
        return $this;
    }


    /**
     * @return string
     */
    public function getTileDefinitionClass()
    {
        return $this->tileDefinitionClass;
    }

    /**
     * @param $tileDefinitionClass
     * @return $this
     */
    public function setTileDefinitionClass($tileDefinitionClass)
    {
        $this->tileDefinitionClass = $tileDefinitionClass;
        return $this;
    }


    /**
     * @return array
     */
    public function getEditLinkParams()
    {
        return $this->editLinkParams;
    }

    /**
     * @param $value
     * @param \DaveBaker\Core\Model\Db\BaseInterface $instance
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getEditLinkHtml(
        $value,
        \DaveBaker\Core\Model\Db\BaseInterface $instance
    ) {
        return $this->getListingUtility()->getEditLinkHtml(
            $this->getEditPageIdentifier(),
            $this->getInstanceIdParam(),
            $value,
            $instance,
            $this->getEditLinkParams()
        );
    }

    /**
     * @param $value
     * @param \DaveBaker\Core\Model\Db\BaseInterface $instance
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDeleteBlockHtml(
        $value,
        \DaveBaker\Core\Model\Db\BaseInterface $instance
    ) {
        return $this->getListingUtility()->getDeleteBlockHtml(
            $this->getInstanceIdParam(),
            $this->getBlockPrefix(),
            $value,
            $instance
        );
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getListingUtilityHelper()
    {
        return $this->createAppObject('\SuttonBaker\Impresario\Helper\Listing\Utility');
    }

    /**
     * @return \SuttonBaker\Impresario\Helper\Listing\Utility
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getListingUtility()
    {
        if(!$this->listingUtility) {
            $this->listingUtility = $this->createAppObject('\SuttonBaker\Impresario\Helper\Listing\Utility');
        }

        return $this->listingUtility;
    }


    /**
     * @return string
     */
    protected function getBlockPrefix()
    {
        return $this->blockPrefix;
    }

    /**
     * @return string
     */
    protected function getInstanceIdParam()
    {
        return $this->idParam;
    }

    /**
     *
     * @param string $blockPrefix
     * @return $this
     */
    public function setBlockPrefix($blockPrefix) 
    {
        $this->blockPrefix = $blockPrefix;
        return $this;
    }

}