<?php

namespace SuttonBaker\Impresario\Layout;

use  \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
use SuttonBaker\Impresario\Definition\Page;
use  \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;

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
    protected $headingName = 'Purchase Orders';

    public function costPrintHandle() {
        $entityInstance = $this->getApp()->getRegistry()->get('model_instance');

        $this->addBlock(
            /** @var \SuttonBaker\Impresario\Block\Cost\PrintPO $printBlock */
            $this->createBlock(
                \SuttonBaker\Impresario\Block\Cost\PrintPO::class,
                "cost.print.table"
            )
            ->setShortcode('body_content')
            ->setCost($entityInstance)
        );
    }
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

        if ($entityId = $this->getRequest()->getParam(self::ID_KEY)) {
            $entityInstance->load($entityId);

            if (!$entityInstance->getId()) {
                return;
            }
        }

        $this->addHeading()->addMessages();

        if ($entityInstance->getId()) {
            $costType = $entityInstance->getCostType();
        } else {
            $costType = $this->getRequest()->getParam(CostDefinition::COST_TYPE_PARAM);
        }

        $costTypeName = $this->getCostHelper()->determineCostTypeName($entityInstance);

        $this->addBlock(
            /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main"
            )
                ->setHeading(
                    $this->getCostHelper()->getActionVerb($entityInstance) . " <strong>Purchase Order for " . $costTypeName . "</strong>"
                )
                ->setShortcode('body_content')
        );

        // Only projects are supported at the moment
        if ($costType === 'project') {
            if ($entityInstance->getId()) {
                $parentId = $entityInstance->getParentId();
            } else {
                $parentId = $this->getRequest()->getParam(CostDefinition::PARENT_ID_PARAM);
            }

            if ($parentId) {
                $projectHref = $this->getUrlHelper()->getPageUrl(
                    Page::PROJECT_EDIT,
                    ['project_id' => $parentId]
                );

                // Create the tab block for the project link
                $tabs = [
                    [
                        'name' => 'Back To Project',
                        'href' => $projectHref,
                        'icon' => ProjectDefinition::ICON
                    ]
                ];

                if($entityInstance->getId()) {
                    $printHref = $this->getUrlHelper()->getPageUrl(
                        Page::COST_PRINT,
                        ['cost_id' => $entityInstance->getId()]
                    );

                    $tabs[] = [
                        'name' => 'Print PO',
                        'href' => $printHref,
                        'icon' => 'fa fa-print'
                    ];
                }

                $tabBlock = $this->getApp()->getBlockManager()->createBlock(
                    \SuttonBaker\Impresario\Block\Core\Tile\Tabs::class,
                    "cost.tile.tabs",
                    'tabs'
                )->setTabs($tabs);

                $mainTile->addChildBlock($tabBlock);
            }
        }


        $mainTile->addChildBlock(
            $this->createBlock(
                \SuttonBaker\Impresario\Block\Cost\Form\Edit::class,
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('cost_edit_form')

        );
    }

    
}
