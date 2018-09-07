<?php

namespace SuttonBaker\Impresario\Block\Invoice\Form;

use \SuttonBaker\Impresario\Definition\Invoice as InvoiceDefintion;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Invoice\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'invoice_id';
    const PREFIX_KEY = 'invoice';
    const PREFIX_NAME = 'Invoice';

    /**
     * @return \DaveBaker\Core\Block\Template|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     */
    protected function _preDispatch()
    {
        $prefixKey = self::PREFIX_KEY;
        $prefixName = self::PREFIX_NAME;

        $heading = "Create {$prefixName}";
        $entityInstance = $this->getApp()->getRegistry()->get('model_instance');
        $editMode = $entityInstance->getId() ? true : false;

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit")->setGroupTemplate('form/group-vertical.phtml');


        $elements = $builder->build([
            [
                'name' => 'invoice_date',
                'labelName' => 'Invoice Date *',
                'class' => 'js-date-picker',
                'rowIdentifier' => 'invoice_date',
                'type' => 'Input\Text',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
                'value' => $this->getDateHelper()->currentDateShortLocalOutput(),
                'attributes' => [
                    'readonly' => 'readonly',
                    'autocomplete' => 'off',
                    'data-date-settings' => json_encode(['minDate' => '-5Y', 'maxDate' => "0"])
                ],
            ], [
                'name' => 'invoice_number',
                'labelName' => 'Invoice Number *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'invoice_number_val',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
            ], [
                'name' => 'value',
                'labelName' => 'Invoice Value *',
                'type' => 'Input\Text',
                'rowIdentifier' => 'invoice_number_val',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => [
                    'button_name' => $editMode ? 'Update Invoice' : 'Create Invoice',
                    'capabilities' => $this->getVariationHelper()->getEditCapabilities()
                ],
                'class' => 'btn-block'
            ], [
                'name' => 'invoice_id',
                'type' => 'Input\Hidden',
                'value' => $entityInstance->getId()
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ]
        ]);

        $this->addChildBlock(array_values($elements));

        if($this->getInvoiceHelper()->currentUserCanEdit() == false) {
            $this->lock();
        }
    }

}