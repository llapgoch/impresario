<?php

namespace SuttonBaker\Impresario\Form\Rule;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Form\Rules
 */
class Client extends \DaveBaker\Form\Validation\Rule\Base
    implements \DaveBaker\Form\Validation\Rule\RuleInterface
{
    protected $mainError = "Please select a value for '{{niceName}}'";
    protected $inputError = "This needs to be a valid client";

    /**
     * @return bool|\DaveBaker\Form\Validation\Error\ErrorInterface
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function validate()
    {
       $client = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Client')->load($this->getValue());

       if(!$client->getId()){
           return $this->createError();
       }

        return true;
    }

}