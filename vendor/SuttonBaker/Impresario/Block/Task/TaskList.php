<?php

namespace SuttonBaker\Impresario\Block\Task;

use \SuttonBaker\Impresario\Definition\Page as PageDefinition;
use \SuttonBaker\Impresario\Definition\Task as TaskDefinition;
/**
 * Class TaskList
 * @package SuttonBaker\Impresario\Block\Task
 */
class TaskList
    extends \SuttonBaker\Impresario\Block\ListBase
    implements \DaveBaker\Core\Block\BlockInterface
{
    const BLOCK_PREFIX = 'task';
    const ID_PARAM = 'task_id';

    /** @var TableContainer */
    protected $tableContainer;
    /**
     * @return \SuttonBaker\Impresario\Block\ListBase|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {

        $this->addChildBlock(
            $this->tableContainer = $this->createBlock(
                '\SuttonBaker\Impresario\Block\Task\TableContainer',
                "{$this->getBlockPrefix()}.list.table.container"
            )->setTileDefinitionClass('\SuttonBaker\Impresario\Block\Core\Tile\Black')
                ->setShowNoItemsMessage(false)
        );
    }

    /**
     * @return \SuttonBaker\Impresario\Block\ListBase|void
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Db\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \Zend_Db_Adapter_Exception
     */
    protected function _preRender()
    {
        if(!count($this->tableContainer->getInstanceCollection()->getItems())){
            $this->tableContainer->getChildBlock('task.table.tile.block')
                ->setTileBodyClass('nopadding')
                ->addChildBlock($this->getNoItemsBlock(null, 'content'));
        }
    }

    /**
     * @return string
     */
    protected function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }

    /**
     * @return string
     */
    protected function getInstanceIdParam()
    {
        return self::ID_PARAM;
    }

    /**
     * @return string
     */
    protected function getEditPageIdentifier()
    {
        return PageDefinition::TASK_EDIT;
    }

}
