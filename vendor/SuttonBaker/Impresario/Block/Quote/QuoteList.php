<?php

namespace SuttonBaker\Impresario\Block\Quote;
/**
 * Class QuoteList
 * @package SuttonBaker\Impresario\Block\Quote
 */
class QuoteList
    extends \SuttonBaker\Impresario\Block\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    const BLOCK_PREFIX = 'quote';
    const COMPLETED_KEY = 'completed';
    /**
     * @return \DaveBaker\Core\Block\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Quote\Collection $instanceCollection */
        $instanceCollection = $this->getQuoteHelper()->getDisplayQuotes();
        $instanceItems = $instanceCollection->load();


        $this->addChildBlock(
            $this->getMessagesBlock()
        );


        if(count($instanceItems)) {
            $headers = array_keys($instanceItems[0]->getData());
            $headers[] = 'edit_column';
            $headers[] = 'delete_column';
            // add edit for each one
            foreach($instanceItems as $instanceItem){
                $instanceItem->setData('edit_column',  $this->getLinkHtml($instanceItem));

                $instanceItem->setData('delete_column', $this->getDeleteBlockHtml($instanceItem->getId()));

                if($instanceItem->getData('created_at')) {
                    $createdDate = $this->getApp()->getHelper('Date')
                        ->utcDbDateTimeToShortLocalOutput($instanceItem->getData('created_at'));

                    $instanceItem->setData('created_at', $createdDate);
                }

                if($instanceItem->getUpdatedAt()) {
                    $updatedAt = $this->getApp()->getHelper('Date')
                        ->utcDbDateTimeToShortLocalOutput($instanceItem->getData('updated_at'));

                    $instanceItem->setData('updated_at', $updatedAt);
                }
            }

            $this->addChildBlock(
                $this->createBlock(
                    '\DaveBaker\Core\Block\Html\Table',
                    "{$this->getBlockPrefix()}.list.table"
                )->setHeaders($headers)->setRecords($instanceItems)->addEscapeExcludes(['edit_column', 'delete_column'])
            );
        }
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $instance
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getEditUrl(\SuttonBaker\Impresario\Model\Db\Quote $instance)
    {
        return $this->getApp()->getHelper('Url')->getPageUrl(
            \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT,
            ['quote_id' => $instance->getId()]
        );
    }

    /**
     * @return string
     */
    protected function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Quote $instance
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getLinkHtml(\SuttonBaker\Impresario\Model\Db\Quote $instance)
    {
        return "<a href={$this->getEditUrl($instance)}>" . $this->escapeHtml('Edit Quote') . "</a>";
    }

    /**
     * @param $instanceId
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getDeleteBlockHtml($instanceId)
    {
        /** @var \DaveBaker\Form\Block\Form $form */
        $form = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Form', "{$this->getBlockPrefix()}.list.delete.{$instanceId}")
            ->setElementName("{$this->getBlockPrefix()}_delete");

        /** @var \DaveBaker\Form\Block\Input\Submit $submit */
        $submit = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Submit', "{$this->getBlockPrefix()}.list.delete.submit.{$instanceId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $id = $this->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "{$this->getBlockPrefix()}.list.delete.id.{$instanceId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $action = $this->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "{$this->getBlockPrefix()}.list.delete.action.{$instanceId}");

        $submit->setElementName('submit')
            ->setElementValue("Delete");

        $id->setElementValue($instanceId)->setElementName("{$this->getBlockPrefix()}_id");
        $action->setElementName('action')->setElementValue('delete');

        $form->addChildBlock([$submit, $id, $action]);

        return $form->render();
    }

}
