<?php
namespace SuttonBaker\Impresario\SaveConverter;

use DaveBaker\Core\Base;

use SuttonBaker\Impresario\SaveConverter\Enquiry as EnquiryConverter;

/**
 * Class Project
 * @package SuttonBaker\Impresario\SaveConverter
 */
class Project extends Base
{
    /**
     * @param array $data
     * @return mixed
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function convert($data)
    {
        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');

        // Convert dates to DB
        if (isset($data['date_received'])){
            $data['date_received'] = $helper->localDateToDb($data['date_received']);
        }

        if(isset($data['date_required'])){
            $data['date_required'] = $helper->localDateToDb($data['date_required']);
        }

        if(isset($data['project_start_date'])){
            $data['project_start_date'] = $helper->localDateToDb($data['project_start_date']);
        }

        if(isset($data['project_end_date'])){
            $data['project_end_date'] = $helper->localDateToDb($data['project_end_date']);
        }
        
        return $data;
    }
}