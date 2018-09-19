<?php

namespace SuttonBaker\Impresario\Layout;
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
    protected $icon = 'fa-ravelry';

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
        );

        $mainTile->addChildBlock(
            $this->createBlock(
                '\SuttonBaker\Impresario\Block\Project\Form\Edit',
                "{$this->getBlockPrefix()}.form.edit",
                'content'
            )->setElementName('project_edit_form')

        );


        if($entityInstance->getQuoteId()) {
            $mainTile->addChildBlock(
                $quoteLink = $mainTile->createBlock(
                    '\DaveBaker\Core\Block\Html\ButtonAnchor',
                    'view.quote.link',
                    'header_elements'
                )->setTagText('View Quote')
                ->addAttribute(
                    ['href' => $this->getRequest()->getUrlHelper()->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::QUOTE_EDIT,
                        ['quote_id' => $entityInstance->getQuoteId()],
                        true
                    )]
                )->setCapabilities($this->getQuoteHelper()->getViewCapabilities())
            );
        }
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function projectListHandle()
    {
        $this->addHeading()->addMessages();

        $this->addBlock(
        /** @var \SuttonBaker\Impresario\Block\Core\Tile\Black $mainTile */
            $mainTile = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Core\Tile\Black',
                "{$this->getBlockPrefix()}.tile.main")
                ->setHeading("Project <strong>List</strong>")
                ->setShortcode('body_content')
                ->setTileBodyClass('nopadding')
        );


        $mainTile->addChildBlock(
            $mainTile->createBlock(
                '\SuttonBaker\Impresario\Block\Project\ProjectList',
                "{$this->getBlockPrefix()}.list",
                'content'
            )
        );
    }
}