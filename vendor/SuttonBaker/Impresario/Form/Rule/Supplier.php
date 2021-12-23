<?php

namespace SuttonBaker\Impresario\Form\Rule;

/**
 * Class Supplier
 * @package SuttonBaker\Impresario\Form\Rules
 */
class Supplier extends \DaveBaker\Form\Validation\Rule\Base
implements \DaveBaker\Form\Validation\Rule\RuleInterface
{
    /** @var string */
    protected $mainError = "Please select a value for '{{niceName}}'";
    /** @var string */
    protected $inputError = "This needs to be a valid supplier";

    /**
     * @return bool|\DaveBaker\Form\Validation\Error\ErrorInterface
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function validate()
    {
        $supplier = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Supplier')->load($this->getValue());

        if (!$supplier->getId()) {
            return $this->createError();
        }

        return true;
    }
}
