<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\Layout
 */
class Enquiry extends \DaveBaker\Core\Layout\Base
{

    /**
     * @throws \DaveBaker\Core\App\Exception
     */
    public function enquiryEditHandle()
    {
        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Enquiry\Form\Edit',
                'enquiry.form.edit'
            )->setElementName('enquiry_edit_form')->setShortcode('body_content')->setFormAction("")

        );
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     */
    public function enquiryListHandle()
    {
        $this->addBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Enquiry\EnquiryList',
                'enquiry.list'
            )->setShortcode('body_content')
        );
    }
}