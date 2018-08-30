<?php

namespace SuttonBaker\Impresario\Block\Client;
/**
 * Class ClientList
 * @package SuttonBaker\Impresario\Block\Client
 */
class ClientList
    extends \SuttonBaker\Impresario\Block\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    /**
     * @return \DaveBaker\Core\Block\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
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

        $this->addChildBlock(
            $this->getMessagesBlock()
        );



        if(count($clientItems)) {
            $tableHeaders = \SuttonBaker\Impresario\Definition\Client::TABLE_HEADERS;


            // add edit for each one
            foreach($clientItems as $client){
                $client->setData('edit_column',  $this->getLinkHtml($client));

                $client->setData('delete_column', $this->getDeleteBlockHtml($client->getId()));

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
                )->setHeaders($tableHeaders)->setRecords($clientItems)->addEscapeExcludes(['edit_column', 'delete_column'])
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
        return $this->getApp()->getHelper('Url')->getPageUrl(
            \SuttonBaker\Impresario\Definition\Page::CLIENT_EDIT, ['client_id' => $client->getId()]
        );
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

    /**
     * @param $clientId
     * @return mixed
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function getDeleteBlockHtml($clientId)
    {
        /** @var \DaveBaker\Form\Block\Form $form */
        $form = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Form', "client.list.delete.{$clientId}")
            ->setElementName('client_delete');

        /** @var \DaveBaker\Form\Block\Input\Submit $submit */
        $submit = $this->getApp()->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Submit', "client.list.delete.submit.{$clientId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $id = $this->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "client.list.delete.id.{$clientId}");

        /** @var \DaveBaker\Form\Block\Input\Hidden $id */
        $action = $this->getBlockManager()->createBlock('\DaveBaker\Form\Block\Input\Hidden', "client.list.delete.action.{$clientId}");

        $submit->setElementName('submit')
            ->setElementValue("Delete");

        $id->setElementValue($clientId)->setElementName('client_id');
        $action->setElementName('action')->setElementValue('delete');

        $form->addChildBlock([$submit, $id, $action]);

        return $form->render();
    }

}
