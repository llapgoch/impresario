<?php
namespace SuttonBaker\Impresario\SaveConverter;

use DaveBaker\Core\Base;


/**
 * Class Project
 * @package SuttonBaker\Impresario\SaveConverter
 */
class Cost extends Base
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
        if (isset($data['cost_date'])) {
            $data['cost_date'] = $helper->localDateToDb($data['cost_date']);
        }
        
        return $data;
    }
}