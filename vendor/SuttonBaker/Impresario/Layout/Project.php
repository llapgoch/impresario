<?php

namespace SuttonBaker\Impresario\Layout;

use \SuttonBaker\Impresario\Definition\Project as ProjectDefinition;
/**
 * Class Project
 * @package SuttonBaker\Impresario\Layout
 */
class Project extends Base
{
    const ID_KEY = 'project_id';

    /** @var string  */
    protected $blockPrefix = 'project';
    /** @var string  */
    protected $headingName = 'Projects';
    protected $icon = \SuttonBaker\Impresario\Definition\Project::ICON;

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Select_Exception
     */
    public function projectEditHandle()
    {
        /** @var \SuttonBaker\Impresario\Model\Db\Project $entityInstance */
        $entityInstance = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Project');

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
                ->setHeading($this->getProjectHelper()->getActionVerb($entityInstance) . " <strong>Project</strong>")
                ->setShortcode('body_content')
                ->addChildBlock($this->getProjectHelper()->getTabBarForProject($entityInstance)
            )
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Project\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('project_edit_form')

        );
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    public function projectListHandle()
    {
        $this->addHeading()->addMessages();

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading("<strong>Project</strong> List")
                ->setShortcode('body_content')
                ->setTileBodyClass('nopadding')
        );

        $instanceCollection = $this->getProjectHelper()->getProjectCollection()
            ->where('status<>?', ProjectDefinition::STATUS_COMPLETE)
            ->addOutputProcessors([
                'invoice_amount_remaining' => $this->getLocaleHelper()->getOutputProcessorCurrency()
            ]);


        $mainTile->addChildBlock(
            $mainTile->createBlock(
                '\SuttonBaker\Impresario\Block\Project\ProjectList',
                "{$this->getBlockPrefix()}.list",
                'content'
            )->setInstanceCollection($instanceCollection)
        );
    }
}