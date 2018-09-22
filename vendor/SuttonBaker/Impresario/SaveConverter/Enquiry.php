<?php
namespace SuttonBaker\Impresario\SaveConverter;

use DaveBaker\Core\Base;

use SuttonBaker\Impresario\SaveConverter\Enquiry as EnquiryConverter;

/**
 * Class Enquiry
 * @package SuttonBaker\Impresario\SaveConverter
 */
class Enquiry extends Base
{
    /**
     * @param $data
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function convert($data)
    {
        /** @var EnquiryConverter $converter */
        $converter = $this->createAppObject(EnquiryConverter::class);
        $helper = $this->getApp()->getHelper('Date');

        // Convert dates to DB
        if (isset($data['date_received'])){
            $data['date_received'] = $helper->localDateToDb($data['date_received']);
        }

        if(isset($data['target_date'])){

            $data['target_date'] = $helper->localDateToDb($data['target_date']);
        }

        if(isset($data['date_completed'])){
            $data['date_completed'] = $helper->localDateToDb($data['date_completed']);
        }

        return $data;
    }
}