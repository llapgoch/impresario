<?php

namespace SuttonBaker\Impresario\Controller\Cost;

use DaveBaker\Core\Definitions\Messages;
use DaveBaker\Core\Definitions\Upload;
use \SuttonBaker\Impresario\Definition\Cost as CostDefinition;
use SuttonBaker\Impresario\Definition\Page;
use SuttonBaker\Impresario\Definition\Roles;
use SuttonBaker\Impresario\Helper\Cost;


/**
 * Class EditController
 * @package SuttonBaker\Impresario\Controller\Cost
 */
class PrintController
    extends \SuttonBaker\Impresario\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    const ENTITY_ID_PARAM = 'cost_id';

    /** @var array  */
    protected $capabilities = [
        Roles::CAP_EDIT_COST,
        Roles::CAP_VIEW_COST,
        Roles::CAP_ALL
    ];

    /** @var \DaveBaker\Form\Block\Form $editForm */
    protected $editForm;
    protected $parentItem;
    protected $costType;
    protected $modelInstance;



    /**
     * @return \DaveBaker\Core\App\Response|object|\SuttonBaker\Impresario\Controller\Base
     * @throws \DaveBaker\Core\Object\Exception
     */
    public function _preDispatch()
    {
        if(!($instanceId = $this->getRequest()->getParam(self::ENTITY_ID_PARAM))){
            return $this->getResponse()->redirectReferer(
                $this->getUrlHelper()->getPageUrl(Page::PROJECT_LIST)
            );
        }

        $this->setModelInstance($this->getCostHelper()->getCost($instanceId));

        if(!$this->modelInstance->getId()) {
            $this->addMessage('The Purchase Order does not exist', Messages::ERROR);
            
            return $this->getResponse()->redirectReferer(
                $this->getUrlHelper()->getPageUrl(Page::PROJECT_LIST)
            );
        }
    }

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Helper\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    public function execute()
    {
       // All done in layout
    }

    /**
     * @param $modelInstance
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function setModelInstance($modelInstance)
    {
        $this->modelInstance = $modelInstance;
        $this->getApp()->getRegistry()->register('model_instance', $modelInstance);
    }

   
}