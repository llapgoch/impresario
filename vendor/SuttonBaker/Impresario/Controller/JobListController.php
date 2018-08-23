<?php

namespace SuttonBaker\Impresario\Controller;

class JobListController
    extends \DaveBaker\Core\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{

    public function execute()
    {
        // Form submission
        if($this->getRequest()->getPostParam('action')){
            /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
            $configurator = $this->createObject('\SuttonBaker\Impresario\Form\Rules\JobConfigurator', [$this->getApp()]);

            /** @var \DaveBaker\Form\Validation\Validator $validator */
            $validator = $this->createObject('\DaveBaker\Form\Validation\Validator', [$this->getApp()])
                ->setValues($this->getRequest()->getPostParams())
                ->configurate($configurator);


            var_dump(count($configurator->getRules()));
            var_dump($validator->validate());
        }
        
    }
}