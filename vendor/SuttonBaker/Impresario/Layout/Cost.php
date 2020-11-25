<?php

namespace SuttonBaker\Impresario\Layout;

use  \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
/**
 * Class Cost
 * @package SuttonBaker\Impresario\Layout
 */
class Cost extends Base
{
    const ID_KEY = 'cost_id';
    const COST_TYPE_KEY = 'cost_type';

    /** @var string  */
    protected $blockPrefix = 'cost';
    /** @var string  */
    protected $icon = \SuttonBaker\Impresario\Definition\Cost::ICON;
    /** @var string  */
    protected $headingName = 'Costs';

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function costEditHandle()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Cost $entityInstance */
        $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Cost');

        if($entityId = $this->getRequest()->getParam(self::ID_KEY)){
            $entityInstance->load($entityId);

            if(!$entityInstance->getId()){
                return;
            }
        }

        $this->addHeading()->addMessages();
        $costTypeName = $this->getCostHelper()->determineCostTypeName($entityInstance);

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading(
                    $this->getCostHelper()->getActionVerb($entityInstance) . " <strong>Cost for " . $costTypeName . "</strong>")
                ->setShortcode('body_content')
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Cost\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('cost_edit_form')

        );

    }

}