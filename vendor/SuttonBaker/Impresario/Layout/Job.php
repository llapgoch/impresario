<?php

namespace SuttonBaker\Impresario\Layout;

class Job extends \DaveBaker\Core\Layout\Base
{
    public function defaultHandle()
    {
//        $this->addBlock($this->getBlockManager()->createBlock(
//            '\SuttonBaker\Impresario\Block\Test',
//            'test.outer'
//        )->setTitle("OUTER")
//            ->setShortcode('body_content')
//            ->addChildBlock($this->getBlockManager()->createBlock(
//                '\SuttonBaker\Impresario\Block\Test',
//                'test.inner'
//            )->setTitle("INNER 1")
//            ));
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