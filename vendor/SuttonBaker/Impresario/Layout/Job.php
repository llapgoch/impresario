<?php

namespace SuttonBaker\Impresario\Layout;

class Job extends \DaveBaker\Core\Layout\Base
{
    public function defaultHandle()
    {

    }

    public function registerHandle()
    {

        
    }

    public function jobListHandle()
    {
        $this->addBlock(
            $this->createBlock('\SuttonBaker\Impresario\Block\Job\Form\Create', 'job.list.form')
                ->setFormName('job_list_form')
                ->setShortcode('body_content')
        );
    }
}