<?php

namespace SuttonBaker\Impresario\Controller;
use DaveBaker\Core\Definitions\Messages;
use \SuttonBaker\Impresario\Definition\Page;

/**
 * Class ClientEditController
 * @package SuttonBaker\Impresario\Controller
 */
class ClientEditController
    extends \DaveBaker\Core\Controller\Base
    implements \DaveBaker\Core\Controller\ControllerInterface
{
    /** @var \DaveBaker\Form\Block\Form $clientEditForm */
    protected $clientEditForm;

    /**
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Event\Exception
     * @throws \DaveBaker\Core\Model\Db\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     * @throws \DaveBaker\Form\Validation\Rule\Configurator\Exception
     */
    public function execute()
    {
        /** @var \DaveBaker\Form\Block\Form $clientEditForm */
        if(!($this->clientEditForm = $this->getApp()->getBlockManager()->getBlock('client.form.edit'))){
            return;
        }

//        wp_enqueue_script('jquery-ui-datepicker');
//        wp_enqueue_style('jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

        $client = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Client');

        // Form submission
        if($this->getRequest()->getPostParam('action')){

            $postParams = $this->getRequest()->getPostParams();

            /** @var \DaveBaker\Form\Validation\Rule\Configurator\ConfiguratorInterface $configurator */
            $configurator = $this->createAppObject('\SuttonBaker\Impresario\Form\ClientConfigurator');

            /** @var \DaveBaker\Form\Validation\Validator $validator */
            $validator = $this->createAppObject('\DaveBaker\Form\Validation\Validator')
                ->setValues($postParams)
                ->configurate($configurator);

            if(!$validator->validate()){
                return $this->prepareFormErrors($validator);
            }

            $clientName = $this->getRequest()->getPostParam('client_name');
            $this->saveFormValues();
            $this->redirectToPage(Page::CLIENT_LIST);
        }


        if($clientId = (int) $this->getRequest()->getParam('client_id')){
            // We're loading, fellas!
            /** @var \SuttonBaker\Impresario\Model\Db\Client $client */
            $client->load($clientId);

            if($client->getIsDeleted()){
                $this->addMessage('The client has been deleted', Messages::ERROR);
                $this->redirectToPage(Page::CLIENT_LIST);
            }

            if(!$client->getId()){
                $this->addMessage('The client does not exist', Messages::ERROR);
                $this->redirectToPage(Page::CLIENT_LIST);
            }
        }

        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Apply the values to the form element
        if($client->getId()) {
            $applicator->configure(
                $this->clientEditForm,
                $client->getData()
            );
        }
    }

    /**
     * @return $this
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function saveFormValues()
    {
        $data = $this->getRequest()->getPostParams();
        $client = $this->createAppObject('\SuttonBaker\Impresario\Model\Db\Client');

        $this->addMessage("The client '{$data["client_name"]}' has been " . ($data['client_id'] ? 'updated' : 'added'));
        $client->setData($data)->save();

        return $this;
    }

    /**
     * @param \DaveBaker\Form\Validation\Validator $validator
     * @throws \DaveBaker\Core\App\Exception
     * @throws \DaveBaker\Core\Block\Exception
     * @throws \DaveBaker\Core\Object\Exception
     * @throws \DaveBaker\Form\Exception
     */
    protected function prepareFormErrors(
        \DaveBaker\Form\Validation\Validator $validator
    ) {
        /** @var \DaveBaker\Form\BlockApplicator $applicator */
        $applicator = $this->createAppObject('\DaveBaker\Form\BlockApplicator');

        // Create main error block
        /** @var \DaveBaker\Form\Block\Error\Main $errorBlock */
        $errorBlock = $this->getApp()->getBlockManager()->createBlock(
            '\DaveBaker\Form\Block\Error\Main',
            'client.edit.form.errors'
        )->setOrder('after', 'client.form.edit.heading')->addErrors($validator->getErrors());

        $this->clientEditForm->addChildBlock($errorBlock);

        // Sets the values back onto the form element
        $applicator->configure(
            $this->clientEditForm,
            $this->getRequest()->getPostParams()
        );
    }
}