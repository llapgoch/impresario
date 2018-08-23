<?php

namespace SuttonBaker\Impresario\Controller;

class DefaultController
    extends \DaveBaker\Core\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{

    public function execute()
    {

        $blocks = $this->getApp()->getBlockManager()->getAllRenderedBlocks();
        $allBlocks = $this->getApp()->getBlockManager()->getAllBlocks();


        $f = function($context){

        };

        $this->addEvent('model_job_create', $f);

        $this->addEvent('model_job_create', [$this, 'callbackBaby']);


        $this->addEvent('wp_login_errors', function(\WP_Error $errors){
           $errors->remove('incorrect_password');
            $errors->add('boogaloo', 'Your horse can\'t whisper at night');

            return $errors;
        });

//        $this->removeEvent('model_job_create', [$this, 'callbackBaby']);
//        $this->removeEvent('model_job_create', $f);

//        $this->removeEvent('model_job_create');

    }

    public function callbackBaby($context)
    {
        var_dump('yeah');
    }

    protected function _postDispatch()
    {
        return parent::_postDispatch();
    }


}