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
    /** @var string */
    protected $blockPrefix = 'upload';
    /** @var \DaveBaker\Core\Model\Db\Core\Upload\Collection $instanceCollection */
    protected $instanceCollection;
    /** @var string */
    protected $tileDefinitionClass = '\SuttonBaker\Impresario\Block\Core\Tile\White';
    /** @var string */
    protected $uploadType;
    /** @var int */
    protected $identifier;


    /**
     * @return string
     */
    public function getUploadType()
    {
        return $this->uploadType;
    }

    /**
     * @param string $uploadType
     * @return $this
     */
    public function setUploadType($uploadType)
    {
        $this->uploadType = $uploadType;
        return $this;
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param int $identifier
     * @return $this
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

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
        $instanceItems = [];
        if(!$this->instanceCollection && $this->getUploadType() && $this->getIdentifier()){
            $this->instanceCollection = $this->getUploadHelper()->getUploadCollection(
                $this->getUploadType(), $this->getIdentifier()
            );

            $instanceItems = $this->instanceCollection->load();
        }

        $this->addChildBlock(
            $tileBlock = $this->createBlock(
                $this->getTileDefinitionClass(),
                "{$this->getBlockPrefix()}.tile.block"
            )->setHeading('<strong>File</strong> Attachments')
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
                    ->setNewWindowLink(true)
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
                    "{$this->getBlockPrefix()}.list.table",
                    'content'
                )->setTagText('No attachments have currently been added')
                    ->setIsReplacerBlock(true)
            );
        }
    }
}