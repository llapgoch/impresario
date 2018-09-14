<?php

namespace SuttonBaker\Impresario\Block\Upload;
use SuttonBaker\Impresario\Definition\Upload as UploadDefinition;

/**
 * Class TableContainer
 * @package SuttonBaker\Impresario\Block\Variation
 */
class TableContainer
    extends \SuttonBaker\Impresario\Block\Table\Container\Base
    implements \DaveBaker\Core\Block\BlockInterface
{
    /** @var string  */
    protected $blockPrefix = 'upload';
    /** @var \DaveBaker\Core\Model\Db\Core\Upload\Collection $instanceCollection */
    protected $instanceCollection;
    /** @var string  */
    protected $tileDefinitionClass = '\SuttonBaker\Impresario\Block\Core\Tile\White';

    /**
     * @return \DaveBaker\Core\Model\Db\Core\Upload\Collection
     */
    public function getInstanceCollection()
    {
        return $this->instanceCollection;
    }

    /**
     * @param \DaveBaker\Core\Model\Db\Core\Upload\Collection $instanceCollection
     * @return $this
     */
    public function setInstanceCollection(
        \DaveBaker\Core\Model\Db\Core\Upload\Collection $instanceCollection
    ) {
        $this->instanceCollection = $instanceCollection;
        return $this;
    }

    /**
     * @return \SuttonBaker\Impresario\Block\Table\Container\Base|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function _preDispatch()
    {

        if(!$this->instanceCollection){
            $this->instanceCollection = $this->getUploadHelper()->getUploadCollection();
        }

        $instanceItems = $this->instanceCollection->load();

        $this->addChildBlock(
            $tileBlock = $this->createBlock(
                $this->getTileDefinitionClass(),
                "{$this->getBlockPrefix()}.tile.block"
            )->setHeading('File <strong>Attachments</strong>')
        );


        if(count($instanceItems)) {
            $tileBlock->setTileBodyClass('nopadding');

            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\SuttonBaker\Impresario\Block\Table\StatusLink',
                    "{$this->getBlockPrefix()}.list.table",
                    'content'
                )->setHeaders(UploadDefinition::TABLE_HEADERS)
                    ->setRecords($this->instanceCollection)
                    ->addClass('table-striped')
            );

            $tableBlock->setLinkCallback(
                function ($headerKey, $record) {
                    return $record->getUrl();
                }
            );
        }else{
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\DaveBaker\Core\Block\Html\Tag',
                    "{$this->getBlockPrefix()}.list.no.records",
                    'content'
                )->setTagText('No attachments have currently been added')
            );
        }
    }
}