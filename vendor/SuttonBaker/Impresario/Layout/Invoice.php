<?php

namespace SuttonBaker\Impresario\Layout;

use  \SuttonBaker\Impresario\Definition\Invoice as InvoiceDefinition;
/**
 * Class Invoice
 * @package SuttonBaker\Impresario\Layout
 */
class Invoice extends Base
{
    const ID_KEY = 'invoice_id';
    const INVOICE_TYPE_KEY = 'invoice_type';

    /** @var string  */
    protected $blockPrefix = 'invoice';
    /** @var string  */
    protected $icon = \SuttonBaker\Impresario\Definition\Invoice::ICON;
    /** @var string  */
    protected $headingName = 'Invoices';

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function invoiceEditHandle()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Invoice $entityInstance */
        $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Invoice');

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            $entityInstance->load($entityId);

            if(!$entityInstance->getId()){
                return;
            }
        }

        $this->addHeading()->addMessages();
        $invoiceTypeName = $this->getInvoiceHelper()->determineInvoiceTypeName($entityInstance);

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading(
                    $this->getInvoiceHelper()->getActionVerb($entityInstance) . " <strong>Invoice for " . $invoiceTypeName . "</strong>")
                ->setShortcode('body_content')
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Invoice\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('invoice_edit_form')

        );

    }

}