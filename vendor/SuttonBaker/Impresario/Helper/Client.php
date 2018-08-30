<?php

namespace SuttonBaker\Impresario\Helper;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Helper
 */
class Client extends Base
{

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Returns a collection of non-deleted clients
     */
    public function getClientCollection()
    {
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Client\Collection'
        );

        $collection->getSelect()->where('is_deleted=?', '0');

        return $collection;
    }
}