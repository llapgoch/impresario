<?php

namespace SuttonBaker\Impresario\Event;
use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Definition\Archive;
use SuttonBaker\Impresario\Definition\Client;
use SuttonBaker\Impresario\Definition\Enquiry;
use SuttonBaker\Impresario\Definition\Page;
use SuttonBaker\Impresario\Definition\Quote;
use SuttonBaker\Impresario\Definition\Task;
use SuttonBaker\Impresario\Definition\Project;

/**
 * Class GlobalEvents
 * @package SuttonBaker\Impresario\Event
 */
class GlobalEvents extends \DaveBaker\Core\Base
{
    public function _construct()
    {
        $this->addEvents();
    }

    /**
     * @throws \DaveBaker\Core\Object\Exception
     */
    protected function addEvents()
    {
        $this->addEvent('body_class', function($classes){
            $classes[] = 'bg-1';
            return $classes;
        });

        $this->addEvent('edit_post_link', function(){
            return '';
        });

        $this->addEvent('login_redirect', function($redirectTo, $request, $user){
            return home_url();
        });

        $event = $this->getApp()->getApiManager()->getRouteEvent(Client::API_ENDPOINT_UPDATE_TABLE);
        $this->addEvent($event, function(){
            $this->getApp()->getHandleManager()->addHandle(Page::CLIENT_LIST);
        });

        $event = $this->getApp()->getApiManager()->getRouteEvent(Enquiry::API_ENDPOINT_UPDATE_TABLE);
        $this->addEvent($event, function(){
            $this->getApp()->getHandleManager()->addHandle(Page::ENQUIRY_LIST);
        });

        $event = $this->getApp()->getApiManager()->getRouteEvent(Quote::API_ENDPOINT_UPDATE_TABLE);
        $this->addEvent($event, function(){
            $this->getApp()->getHandleManager()->addHandle(Page::QUOTE_LIST);
        });

        $event = $this->getApp()->getApiManager()->getRouteEvent(Project::API_ENDPOINT_UPDATE_TABLE);
        $this->addEvent($event, function(){
            $this->getApp()->getHandleManager()->addHandle(Page::PROJECT_LIST);
        });

        $event = $this->getApp()->getApiManager()->getRouteEvent(Task::API_ENDPOINT_UPDATE_TABLE);
        $this->addEvent($event, function(){
            $this->getApp()->getHandleManager()->addHandle(Page::TASK_LIST);
        });

        $event = $this->getApp()->getApiManager()->getRouteEvent(Archive::API_ENDPOINT_UPDATE_TABLE);
        $this->addEvent($event, function(){
            $this->getApp()->getHandleManager()->addHandle(Page::ARCHIVE_LIST);
        });

    }
}