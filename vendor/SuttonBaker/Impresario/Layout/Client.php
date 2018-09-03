<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Client
 * @package SuttonBaker\Impresario\Layout
 */
class Client extends Base
{
    const ID_KEY = 'client_id';

    /** @var string  */
    protected $blockPrefix = 'client';

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function clientEditHandle()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Task $entityInstance */
        $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Client');

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            $entityInstance->load($entityId);
            $editMode = true;
        }


        $this->addBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Heading',
                "{$this->getBlockPrefix()}.form.edit.heading")
                ->setHeading('Clients')
                ->setTemplate('core/main-header.phtml')
                ->setShortcode('body_content')
        );

        $this->addBlock(
            $this->getBlockManager()->getMessagesBlock()->setShortcode('body_content')
        );

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading($entityId ? "Update Client" : "Create a New Client")
                ->setShortcode('body_content')
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Client\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName("{$this->getBlockPrefix()}_edit_form")

        );
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function clientListHandle()
    {

        $this->addBlock(
            $this->createBlock(
                '\DaveBaker\Core\Block\Html\Heading',
                "{$this->getBlockPrefix()}.form.edit.heading")
                ->setHeading('Clients')
                ->setTemplate('core/main-header.phtml')
                ->setShortcode('body_content')
        );

        $this->addBlock(
            $this->getBlockManager()->getMessagesBlock()->setShortcode('body_content')
        );

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading("Client List")
                ->setShortcode('body_content')
        );

        $mainTile->addChildBlock(
            $createLink = $mainTile->createBlock(
                '\DaveBaker\Core\Block\Html\ButtonAnchor',
                'create.client.link',
                'header_elements'
            )
                ->setTagText('Create a New Client')
                ->addAttribute(
                    ['href' => $this->getRequest()->getUrlHelper()->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::CLIENT_EDIT
                    )]
                )
        );

        $mainTile->addChildBlock(
            $mainTile->createBlock(
                '\SuttonBaker\Impresario\Block\Client\ClientList',
                'client.list',
                'content'
            )
        );
    }
}