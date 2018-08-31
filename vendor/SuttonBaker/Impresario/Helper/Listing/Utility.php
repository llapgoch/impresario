<?php

namespace SuttonBaker\Impresario\Helper\Listing;
/**
 * Class ListBase
 * @package SuttonBaker\Impresario\Block
 */
class Utility
    extends \SuttonBaker\Impresario\Helper\Base
{
    /**
     * @param string $pageIdentifier
     * @param string $instanceIdParam
     * @param mixed $value
     * @param \DaveBaker\Core\Model\Db\BaseInterface $instance
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getEditLinkHtml(
        $pageIdentifier,
        $instanceIdParam,
        $value,
        \DaveBaker\Core\Model\Db\BaseInterface $instance
    ) {
        return "<a href={$this->getEditUrl($pageIdentifier, $instanceIdParam, $instance)}>" . $this->escapeHtml('Edit') . "</a>";
    }

    /**
     * @param mixed $value
     * @param \DaveBaker\Core\Model\Db\BaseInterface $instance
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDeleteBlockHtml(
        $instanceIdParam,
        $blockPrefix,
        $value,
        \DaveBaker\Core\Model\Db\BaseInterface $instance
    ) {
        $instanceId = $instance->getId();

        /** @var \DaveBaker\Form\Block\Form $form */
        $form = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Form', "{$blockPrefix}.list.delete.{$instanceId}")
            ->setElementName("{$blockPrefix}_list_delete");

        /** @var \DaveBaker\Form\Block\Input\Submit $submit */
        $submit = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Submit', "{$blockPrefix}.list.delete.submit.{$instanceId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $id = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "{$blockPrefix}.list.delete.id.{$instanceId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $action = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "{$blockPrefix}.list.delete.action.{$instanceId}");

        $submit->setElementName('submit')
            ->setElementValue("Delete");

        $id->setElementValue($instanceId)->setElementName($instanceIdParam);
        $action->setElementName('action')->setElementValue('delete');

        $form->addChildBlock([$submit, $id, $action]);

        return $form->render();
    }

    /**
     * @param \DaveBaker\Core\Model\Db\BaseInterface $instance
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getEditUrl(
        $pageIdentifier,
        $instanceIdParam,
        \DaveBaker\Core\Model\Db\BaseInterface $instance)
    {
        return $this->getApp()->getHelper('Url')->getPageUrl(
            $pageIdentifier,
            [$instanceIdParam => $instance->getId()]
        );
    }
}