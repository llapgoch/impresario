<?php

namespace SuttonBaker\Impresario\Helper;

use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class Client
 * @package SuttonBaker\Impresario\Helper
 */
class Client extends Base
{
    /** @var array  */
    protected $editCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_CLIENT];

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