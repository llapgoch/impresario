<?php

namespace SuttonBaker\Impresario\Layout;
/**
 * Class Variation
 * @package SuttonBaker\Impresario\Layout
 */
class Variation extends Base
{
    const ID_KEY = 'variation_id';

    /** @var string  */
    protected $blockPrefix = 'variation';
    /** @var string  */
    protected $icon = 'fa-dot-circle-o';
    protected $headingName = 'Variations';

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function variationEditHandle()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Variation $entityInstance */
        $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Variation');

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            $entityInstance->load($entityId);

            if(!$entityInstance->getId()){
                return;
            }
        }

        $this->addHeading()->addMessages();

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading('<strong>Update</strong> Variation')
                ->setShortcode('body_content')
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Variation\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('variation_edit_form')

        );

    }
    
}