<?php

namespace SuttonBaker\Impresario\SaveConverter;

use SuttonBaker\Impresario\Api\Base;

/**
 * Class Quote
 * @package SuttonBaker\Impresario\SaveConverter
 */
class Quote
    extends Base
{
    /**
     * @param $data
     * @return mixed
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function convert($data)
    {
        /** @var \DaveBaker\Core\Helper\Date $helper */
        $helper = $this->getApp()->getHelper('Date');


        if (isset($data['date_received'])){
            $data['date_received'] = $helper->localDateToDb($data['date_received']);
        }

        if(isset($postParams['date_required'])){
            $data['date_required'] = $helper->localDateToDb($data['date_required']);
        }

        if(isset($postParams['date_return_by'])){
            $data['date_return_by'] = $helper->localDateToDb($data['date_return_by']);
        }

        if(isset($postParams['date_returned'])){
            $data['date_returned'] = $helper->localDateToDb($data['date_returned']);
        }

        if(isset($postParams['date_completed'])){
            $data['date_completed'] = $helper->localDateToDb($data['date_completed']);
        }

        return $data;
    }
}