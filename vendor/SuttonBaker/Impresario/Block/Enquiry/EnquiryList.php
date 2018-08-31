<?php

namespace SuttonBaker\Impresario\Block\Enquiry;
/**
 * Class EnquiryList
 * @package SuttonBaker\Impresario\Block\Enquiry
 */
class EnquiryList
    extends \SuttonBaker\Impresario\Block\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    /**
     * @return \SuttonBaker\Impresario\Block\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        $tableHeaders = \SuttonBaker\Impresario\Definition\Enquiry::TABLE_HEADERS;

        /** @var \SuttonBaker\Impresario\Model\Db\Enquiry\Collection $enquiryCollection */
        $enquiryCollection = $this->getEnquiryHelper()->getEnquiryCollection()
            ->addOutputProcessors([
                'date_received' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'target_date' => $this->getDateHelper()->getOutputProcessorFullDate(),
                'status' => $this->getEnquiryHelper()->getStatusOutputProcessor(),
                'edit_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getLinkHtml']),
                'delete_column' => $this->getCustomOutputProcessor()->setCallback([$this, 'getDeleteBlockHtml'])
            ]);

        $this->addChildBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Template',
                'enquiry.list.action.bar'
            )->setTemplate('enquiry/list/action_bar.phtml')
        );

        $this->addChildBlock($this->getMessagesBlock());

        $this->addChildBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Table',
                'enquiry.list.table'
            )->setHeaders($tableHeaders)->setRecords($enquiryCollection->load())->addEscapeExcludes(['edit_column', 'delete_column'])
        );
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getEditUrl(\SuttonBaker\Impresario\Model\Db\Enquiry $enquiry)
    {
        return $this->getApp()->getHelper('Url')->getPageUrl(
            \SuttonBaker\Impresario\Definition\Page::ENQUIRY_EDIT,
            ['enquiry_id' => $enquiry->getId()]
        );
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Enquiry\ $enquiry
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getLinkHtml(
        $value,
        \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry
    ) {
        return "<a href={$this->getEditUrl($enquiry)}>" . $this->escapeHtml('Edit Enquiry') . "</a>";
    }

    /**
     * @param $enquiryId
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getDeleteBlockHtml(
        $value,
        \SuttonBaker\Impresario\Model\Db\Enquiry $enquiry
    ) {
        $enquiryId = $enquiry->getId();

        /** @var \DaveBaker\Form\Block\Form $form */
        $form = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Form', "enquiry.list.delete.{$enquiryId}")
            ->setElementName('enquiry_delete');

        /** @var \DaveBaker\Form\Block\Input\Submit $submit */
        $submit = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Submit', "enquiry.list.delete.submit.{$enquiryId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $id = $this->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "enquiry.list.delete.id.{$enquiryId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $action = $this->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "enquiry.list.delete.action.{$enquiryId}");

        $submit->setElementName('submit')
            ->setElementValue("Delete");

        $id->setElementValue($enquiryId)->setElementName('enquiry_id');
        $action->setElementName('action')->setElementValue('delete');

        $form->addChildBlock([$submit, $id, $action]);

        return $form->render();
    }

}
