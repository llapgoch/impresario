<?php

namespace SuttonBaker\Impresario\Helper;
use DaveBaker\Core\Block\Template;

/**
 * Class Modal
 * @package DaveBaker\Core\Helper
 */
class Modal
    extends \SuttonBaker\Impresario\Helper\Base
{
    /**
     * @param $heading
     * @param $body
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createAutoOpenModal(
        $heading,
        $body
    ) {
        return $this->createModalPlaceholder()
            ->setHeading($heading)
            ->setBody($body)
            ->addClass('js-modal-auto-show');
    }

    /**
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function createModalPlaceholder()
    {
        $this->getApp()->getBlockManager()->deleteBlock('global.modal');

        return $this->getApp()->getBlockManager()->createBlock(
            Template::class,
            'global.modal'
        )->setTemplate('components/modal.phtml')
            ->setIsReplacerBlock(true)
            ->addTagIdentifier('modal');
    }
}