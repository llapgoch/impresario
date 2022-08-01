<?php

namespace SuttonBaker\Impresario\Block\Upload;

use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Definition\Upload as UploadDefinition;
use SuttonBaker\Impresario\Helper\OutputProcessor\Upload\Icon;
use SuttonBaker\Impresario\Helper\OutputProcessor\Upload\Remove as RemoveLink;

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
    /** @var string */
    protected $heading = '<strong>File</strong> Attachments';
    /** @var \DaveBaker\Core\Model\Db\Core\Upload\Collection $instanceCollection */
    protected $instanceCollection;
    /** @var string */
    protected $tileDefinitionClass = '\SuttonBaker\Impresario\Block\Core\Tile\White';
    /** @var string */
    protected $uploadType;
    /** @var int */
    protected $identifier;
    /** @var int */
    protected $recordsPerPage = UploadDefinition::RECORDS_PER_PAGE;
    /** @var bool */
    protected $showDelete = true;

    /**
     *
     * @param string $heading
     * @return $this
     */
    public function setHeading($heading)
    {
        $this->heading = $heading;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     *
     * @param bool $heading
     * @return $this
     */
    public function setShowDelete($showDelete)
    {
        $this->showDelete = (bool) $showDelete;
        return $this;
    }

    /**
     *
     * @return bool
     */
    public function getShowDelete()
    {
        return $this->showDelete;
    }

    /**
     * @return int
     */
    public function getRecordsPerPage()
    {
        return $this->recordsPerPage;
    }

    /**
     * @param int $recordsPerPage
     * @return $this
     */
    public function setRecordsPerPage($recordsPerPage)
    {
        $this->recordsPerPage = $recordsPerPage;
        return $this;
    }

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

        wp_register_script(
            'impresario_file_upload_helper',
            get_template_directory_uri() . '/assets/js/file.uploader.helper.js',
            ['jquery', 'jquery-ui-widget']
        );

        wp_enqueue_script('dbwpcore_table_updater');
        wp_enqueue_script('impresario_deleter');
        wp_enqueue_script('impresario_file_upload_helper');

        $instanceItems = [];
        if (!$this->instanceCollection && $this->getUploadType() && $this->getIdentifier()) {
            $this->instanceCollection = $this->getUploadHelper()->getUploadCollection(
                $this->getUploadType(),
                $this->getIdentifier()
            );

            $instanceItems = $this->instanceCollection->load();
        }

        /* TODO: Remove after testing API */
        if (!$this->instanceCollection) {
            $this->instanceCollection = $this->getUploadHelper()->getUploadCollection();
        }

        $this->instanceCollection->addOutputProcessors([
            'icon' => $this->createAppObject(Icon::class),
            'remove' => $this->createAppObject(RemoveLink::class)
        ]);

        $this->addChildBlock(
            $tileBlock = $this->createBlock(
                $this->getTileDefinitionClass(),
                "{$this->getBlockPrefix()}.tile.block"
            )->setHeading($this->getHeading())
                ->addClass('js-file-upload-container')
                ->setTileBodyClass('nopadding table-responsive')
        );

        $tileBlock->addChildBlock(
            /** @var Paginator $paginator */
            $paginator = $this->createBlock(
                '\DaveBaker\Core\Block\Components\Paginator',
                "{$this->getBlockPrefix()}.list.paginator",
                'footer'
            )->setRecordsPerPage($this->getRecordsPerPage())
                ->setTotalRecords(count($instanceItems))
                ->setIsReplacerBlock(true)
                ->removeClass('pagination-xl')->addClass('pagination-xs')
        );

        // Use the blockPrefix when naming children for multiple uploaders on the same page 
        $jsDataItems = [
            Table::ELEMENT_JS_DATA_KEY_TABLE_UPDATER_ENDPOINT =>
            $this->getUrlHelper()->getApiUrl(
                UploadDefinition::API_ENDPOINT_UPDATE_TABLE,
                [
                    'upload_type' => $this->getUploadType(),
                    'parent_id' => $this->getIdentifier(),
                    'block_prefix' => $this->getBlockPrefix(),
                    'show_delete' => $this->getShowDelete() ? "1" : "0"
                ]
            )
        ];

        $headers = UploadDefinition::TABLE_HEADERS;

        // A cheapo way to remove the delete option when required
        if (!$this->getShowDelete()) {
            unset($headers['remove']);
        }

        if (count($instanceItems)) {
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\SuttonBaker\Impresario\Block\Table\StatusLink',
                    "{$this->getBlockPrefix()}.list.table",
                    'content'
                )->setHeaders($headers)
                    ->setRecords($this->instanceCollection)
                    ->addClass('table-striped js-table-updater-file')
                    ->setNewWindowLink(true)
                    ->addEscapeExcludes(['icon', 'remove'])
                    ->setThAttributes('icon', ['style' => 'width:20px'])
                    ->setThAttributes('remove', ['style' => 'width:70px'])
                    ->addJsDataItems($jsDataItems)
                    ->setPaginator($paginator)
            );

            $tableBlock->setLinkCallback(
                function ($headerKey, $record) {
                    return $record->getUrl();
                }
            );
        } else {
            // Add the jsDataItems as though this were a table so we can update it later
            $tileBlock->addChildBlock(
                $tableBlock = $tileBlock->createBlock(
                    '\DaveBaker\Core\Block\Html\Tag',
                    "{$this->getBlockPrefix()}.list.table",
                    'content'
                )->setTagText('No attachments have currently been added')
                    ->setIsReplacerBlock(true)
                    ->addJsDataItems($jsDataItems)
                    ->addClass('js-table-updater js-table-updater-file px-3')
            );
        }
    }
}
