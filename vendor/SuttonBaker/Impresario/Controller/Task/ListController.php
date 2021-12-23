<?php

namespace SuttonBaker\Impresario\Controller\Task;
use DaveBaker\Core\Definitions\Messages;
use SuttonBaker\Impresario\Definition\Roles;

/**
 * Class ListController
 * @package SuttonBaker\Impresario\Controller\Task
 */
class ListController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const DELETE_ACTION = 'delete';
    /** @var array  */
    protected $capabilities = [
        Roles::CAP_VIEW_TASK,
        Roles::CAP_EDIT_TASK,
        Roles::CAP_ALL
    ];

    /**
     * @return \DaveBaker\Core\Controller\Base|void
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function _preDispatch()
    {
        parent::_preDispatch();
        $this->addEvents();
    }

    /**
     * @throws \DaveBaker\Core\Object\Exception
     * @return $this
     */
    protected function addEvents()
    {
        // Don't display tasks which have been superseded
        $this->addEvent('block_predispatch_before_task_list_table_container', function($context){
            $context->getObject()->setShowSuperseded(false);
        });

        return $this;
    }

    public function execute(){}
}