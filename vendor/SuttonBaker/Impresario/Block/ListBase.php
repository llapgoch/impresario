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

    /**
     * @param \DaveBaker\Core\Model\Db\BaseInterface $instance
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getLinkHtml(
        $value,
        \DaveBaker\Core\Model\Db\BaseInterface $instance
    ) {
        return "<a href={$this->getEditUrl($instance)}>" . $this->escapeHtml('Edit') . "</a>";
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
        $value,
        \DaveBaker\Core\Model\Db\BaseInterface $instance
    ) {
        $instanceId = $instance->getId();
        $prefix = $this->getInstanceIdParam();

        /** @var \DaveBaker\Form\Block\Form $form */
        $form = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Form', "{$prefix}.list.delete.{$instanceId}")
            ->setElementName("{$prefix}_list_delete");

        /** @var \DaveBaker\Form\Block\Input\Submit $submit */
        $submit = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Submit', "{$prefix}.list.delete.submit.{$instanceId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $id = $this->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "{$prefix}.list.delete.id.{$instanceId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $action = $this->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "{$prefix}.list.delete.action.{$instanceId}");

        $submit->setElementName('submit')
            ->setElementValue("Delete");

        $id->setElementValue($instanceId)->setElementName($this->getInstanceIdParam());
        $action->setElementName('action')->setElementValue('delete');

        $form->addChildBlock([$submit, $id, $action]);

        return $form->render();
    }

    /**
     * @param \DaveBaker\Core\Model\Db\BaseInterface $instance
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getEditUrl(\DaveBaker\Core\Model\Db\BaseInterface $instance)
    {
        return $this->getApp()->getHelper('Url')->getPageUrl(
            $this->getEditPageIdentifier(),
            [$this->getInstanceIdParam() => $instance->getId()]
        );
    }
}