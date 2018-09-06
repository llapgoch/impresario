<?php

namespace SuttonBaker\Impresario\Block\Variation\Form;

use \SuttonBaker\Impresario\Definition\Variation as VariationDefinition;

/**
 * Class Edit
 * @package SuttonBaker\Impresario\Block\Invoice\Form
 */
class Edit extends \SuttonBaker\Impresario\Block\Form\Base
{
    const ID_KEY = 'variation_id';
    const PREFIX_KEY = 'variation';
    const PREFIX_NAME = 'Variation';

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
        $editMode = false;

        $entityInstance = $this->getApp()->getRegistry()->get('model_instance');

        if($entityInstance->getId()){
            $editMode = true;
        }

        /** @var \DaveBaker\Form\Builder $builder */
        $builder = $this->createAppObject('\DaveBaker\Form\Builder')
            ->setFormName("{$prefixKey}_edit")->setGroupTemplate('form/group-vertical.phtml');


        $elements = $builder->build([
            [
                'name' => 'date_approved',
                'labelName' => 'Date Approved *',
                'class' => 'js-date-picker',
                'rowIdentifier' => 'date_approved',
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
                'name' => 'value',
                'labelName' => 'Variation Value',
                'type' => 'Input\Text',
                'rowIdentifier' => 'variation_value',
                'formGroup' => true,
                'formGroupSettings' => [
                    'class' => 'col-md-6'
                ],
            ], [
                'name' => 'description',
                'labelName' => 'Description',
                'type' => 'Textarea',
                'rowIdentifier' => 'description',
                'formGroup' => true
            ], [
                'name' => 'submit',
                'type' => '\DaveBaker\Form\Block\Button',
                'data' => ['button_name' => $editMode ? 'Update Variation' : 'Create Variation'],
                'class' => 'btn-block'
            ], [
                'name' => 'variation_id',
                'type' => 'Input\Hidden',
                'value' => $entityInstance->getId()
            ], [
                'name' => 'action',
                'type' => 'Input\Hidden',
                'value' => 'edit'
            ]
        ]);

        $this->addChildBlock(array_values($elements));
    }

}