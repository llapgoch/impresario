<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Supplier
 * @package SuttonBaker\Impresario\Layout
 */
class Supplier extends Base
{
    const ID_KEY = 'supplier_id';

    /** @var string  */
    protected $blockPrefix = 'supplier';
    protected $headingName = 'Supplier';
    protected $icon = \SuttonBaker\Impresario\Definition\Supplier::ICON;

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function supplierEditHandle()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Supplier $entityInstance */
        $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Supplier');

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            $entityInstance->load($entityId);
            $editMode = true;
        }

        $this->addHeading();
        $this->addMessages();


        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading($this->getSupplierHelper()->getActionVerb($entityInstance) . " <strong>Supplier</strong>")
                ->setShortcode('body_content')
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Supplier\Form\Edit',
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
    public function supplierListHandle()
    {

        $this->addHeading();
        $this->addMessages();

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading("<strong>Supplier</strong> List")
                ->setShortcode('body_content')
                ->setTileBodyClass('nopadding table-responsive')
        );

        $mainTile->addChildBlock(
            $createLink = $mainTile->createBlock(
                '\DaveBaker\Core\Block\Html\ButtonAnchor',
                'create.supplier.link',
                'header_elements'
            )
                ->setTagText('Create a New Supplier')
                ->addAttribute(
                    ['href' => $this->getRequest()->getUrlHelper()->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::SUPPLIER_EDIT
                    )]
                )->setCapabilities($this->getSupplierHelper()->getEditCapabilities())
        );

        $mainTile->addChildBlock(
            $mainTile->createBlock(
                '\SuttonBaker\Impresario\Block\Supplier\SupplierList',
                'supplier.list',
                'content'
            )
        );
    }


}