<?php

namespace SuttonBaker\Impresario\Block\Variation;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Variation as VariationDefinition;
/**
 * Class TableContainer
 * @package SuttonBaker\Impresario\Block\Variation
 */
class TableContainer
    extends \SuttonBaker\Impresario\Block\Table\Container\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    /** @var string  */
    protected $blockPrefix = 'variation';
    /** @var string  */
    protected $idParam = 'variation_id';

    /** @var \SuttonBaker\Impresario\Model\Db\Variation\Collection $instanceCollection */
    protected $instanceCollection;
    /** @var string  */
    protected $tileDefinitionClass = '\SuttonBaker\Impresario\Block\Core\Tile\White';


    /**
     * @return \SuttonBaker\Impresario\Model\Db\Variation\Collection
     */
    public function getInstanceCollection()
    {
        return $this->instanceCollection;
    }

    /**
     * @param \SuttonBaker\Impresario\Model\Db\Variation\Collection $instanceCollection
     * @return $this
     */
    public function setInstanceCollection(
        \SuttonBaker\Impresario\Model\Db\Variation\Collection $instanceCollection
    ) {
        $this->instanceCollection = $instanceCollection;
        return $this;
    }

    /**
     * @return \SuttonBaker\Impresario\Block\Table\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {

        if(!$this->instanceCollection){
            $this->instanceCollection = $this->getVariationHelper()->getVariationCollection();
        }

        $this->instanceCollection->addOutputProcessors([
                'date_approved' => $this->getDateHelper()->getOutputProcessorShortDate(),
                'created_at' => $this->getDateHelper()->getOutputProcessorShortDate(),
            ]);

        $instanceItems = $this->instanceCollection->load();

        $this->addChildBlock(
            $tileBlock = $this->createBlock(
                $this->getTileDefinitionClass(),
                "{$this->getBlockPrefix()}.tile.block"
            )->setHeading('<strong>Variations</strong>')
        );


        if(count($instanceItems)) {
            $tileBlock->setTileBodyClass('nopadding');

            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\SuttonBaker\Impresario\Block\Table\StatusLink',
                    "{$this->getBlockPrefix()}.list.table",
                    'content'
                )->setHeaders(VariationDefinition::TABLE_HEADERS)->setRecords($this->instanceCollection)
            );

            $tableBlock->setLinkCallback(
                function ($headerKey, $record) {
                    return $this->getPageUrl(
                        \SuttonBaker\Impresario\Definition\Page::VARIATION_EDIT,
                        [$this->getInstanceIdParam() => $record->getId()],
                        true
                    );
                }
            );
        }else{
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\DaveBaker\Core\Block\Html\Tag',
                    "{$this->getBlockPrefix()}.list.no.records",
                    'content'
                )->setTagText('No variations have currently been created')
            );
        }
    }
}