<?php

namespace SuttonBaker\Impresario\Form\Rules;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Form\Rules
 */
class Client extends \DaveBaker\Form\Validation\Rule\Base
    implements RuleInterface
{
    protected $mainError = "{{niceName}} should be a valid client";
    protected $inputError = "This needs to be a valid client";

    /**
     * @return bool|\DaveBaker\Form\Validation\Error\ErrorInterface
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function validate()
    {
       $user = $this->getApp()->getHelper('User')->getUser($this->getValue());

       if(!$user->getId()){
           return $this->createError();
       }

        return true;
    }

}