<?php

namespace SuttonBaker\Impresario\Block;
/**
 * Class ListBase
 * @package SuttonBaker\Impresario\Block
 */
abstract class ListBase extends Base
{
    protected abstract function getInstanceIdParam();
    protected abstract function getEditPageIdentifier();
    protected abstract function getBlockPrefix();

    /** @var \SuttonBaker\Impresario\Helper\Listing\Utility */
    protected $listingUtility;

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
            $instance
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
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getNoItemsBlock($name = null, $asName = null)
    {
        return $this->createBlock(
            '\SuttonBaker\Impresario\Block\Form\LargeMessage',
            $name,
            $asName
            )->setMessage("There are currently no items to display")
            ->setHeading('There isn\'t Anything Here')
                ->setMessageType('info')
                ->addClass('m-3');
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
}