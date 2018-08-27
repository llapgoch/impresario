<?php

namespace SuttonBaker\Impresario\Block\Client;
/**
 * Class ClientList
 * @package SuttonBaker\Impresario\Block\Client
 */
class ClientList
    extends \DaveBaker\Core\Block\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    /**
     * @return \DaveBaker\Core\Block\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Client\Collection $clients */
        $clientCollection = $this->createAppObject(
            '\SuttonBaker\Impresario\Model\Db\Client\Collection');

        $clientCollection->getSelect()->where('is_deleted = ?', '0');
        $clientItems = $clientCollection->load();

        $this->addChildBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Template',
                'client.list.action.bar'
            )->setTemplate('client/list/action_bar.phtml')
        );

        if(count($clientItems)) {
            $headers = array_keys($clientItems[0]->getData());
            $headers[] = 'edit_column';
            // add edit for each one
            foreach($clientItems as $client){
                $client->setData('edit_column',  $this->getLinkHtml($client));

                if($client->getData('created_at')) {
                    $createdDate = $this->getApp()->getHelper('Date')
                        ->utcDbDateTimeToShortLocalOutput($client->getData('created_at'));

                    $client->setData('created_at', $createdDate);
                }

                if($client->getUpdatedAt()) {
                    $updatedAt = $this->getApp()->getHelper('Date')
                        ->utcDbDateTimeToShortLocalOutput($client->getData('updated_at'));

                    $client->setData('updated_at', $updatedAt);
                }
            }

            $this->addChildBlock(
                $this->createBlock(
                    '\DaveBaker\Core\Block\Html\Table',
                    'client.list.table'
                )->setHeaders($headers)->setRecords($clientItems)->addEscapeExcludes('edit_column')
            );
        }
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Client $client
     * @return mixed
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getClientEditUrl(\SuttonBaker\Impresario\Model\Db\Client $client)
    {
        return $this->getApp()->getHelper('Url')->getPageUrl('client_edit', ['client_id' => $client->getId()]);
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Client $client
     * @return string
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getLinkHtml(\SuttonBaker\Impresario\Model\Db\Client $client)
    {
        return "<a href={$this->getClientEditUrl($client)}>" . $this->escapeHtml('Edit Client') . "</a>";
    }

}
