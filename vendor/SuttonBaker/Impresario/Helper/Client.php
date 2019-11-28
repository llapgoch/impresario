<?php

namespace SuttonBaker\Impresario\Helper;

use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Definition\Client as ClientDefinition;

/**
 * Class Client
 * @package SuttonBaker\Impresario\Helper
 */
class Client extends Base
{
    /** @var array  */
    protected $editCapabilities = [Roles::CAP_ALL, Roles::CAP_EDIT_CLIENT];
    protected $viewCapabilities = [Roles::CAP_ALL, Roles::CAP_VIEW_CLIENT, Roles::CAP_EDIT_CLIENT];

    /**
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     *
     * Returns a collection of non-deleted clients
     */
    public function getClientCollection()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Client\Collection $collection */
        $collection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Client\Collection'
        );

        $collection->getSelect()->where('is_deleted=?', '0');
        $collection->getSelect()->order('client_name');

        return $collection;
    }

    /**
     * @param int|null $clientId
     * @return \SuttonBaker\Impresario\Model\Db\Client
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function getClient($clientId = null)
    {
        $client = $this->createAppObject(ClientDefinition::DEFINITION_MODEL);

        if($clientId){
            $client->load($clientId);
        }

        return $client;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Client $client
     * @return $this
     */
    public function deleteClient(
        \SuttonBaker\Impresario\Model\Db\Client $client
    ) {
        if(!$client->getId()){
            return $this;
        }

        $client->setIsDeleted(1)->save();
        return $this;
    }
}