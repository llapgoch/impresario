<?php

namespace SuttonBaker\Impresario\Layout;

class Login extends Base
{

    protected $blockPrefix = 'login';

    public function loginHandle()
    {
        $this->addBlock(
                $this->createBlock(
                    '\DaveBaker\Core\Block\Template',
                    "{$this->getBlockPrefix()}.header.wrap"
                )->setAction('login_header')->setTemplate('login/header.phtml')
            );

            $this->addBlock(
                    $this->createBlock(
                        '\DaveBaker\Core\Block\Template',
                        "{$this->getBlockPrefix()}.footer.wrap")
                        ->setAction('login_footer')
                        ->setTemplate('login/footer.phtml')
            );

            $this->addBlock(
                    $this->createBlock(
                        '\DaveBaker\Core\Block\Template',
                        "{$this->getBlockPrefix()}.template.elements")
                        ->setAction('login_footer')
                        ->setTemplate('login/template.phtml')
            );
    }
}