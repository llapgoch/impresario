<?php

namespace SuttonBaker\Impresario\Event;
use DaveBaker\Core\Block\BlockInterface;
use DaveBaker\Core\Block\Html\Heading;
use DaveBaker\Core\Definitions\Table;
use SuttonBaker\Impresario\Definition\Archive;
use SuttonBaker\Impresario\Definition\Client;
use SuttonBaker\Impresario\Definition\Supplier;
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

        $this->addEvent('controller_index_execute', function(){
            /** @var Heading $header */
            if($header = $this->getApp()->getBlockManager()->getBlock('index.heading')){
                $header->addClass('text-center');
            }
        });

        $this->addEvent('wp_enqueue_scripts', function(){
            wp_deregister_script('jquery-ui-widget');
            wp_register_script('jquery-ui-widget', get_template_directory_uri() . '/assets/js/jquery/jquery-ui-widget.min.js', ['jquery'], '1.12.1');
            wp_enqueue_script('jquery-ui-widget');

            wp_register_script('dbaker-is-mobile', get_template_directory_uri() . '/assets/js/db/ismobile.js');
            wp_enqueue_script('dbaker-is-mobile');
        });

        $this->addEvent('login_redirect', function($redirectTo, $request, $user){
            return home_url();
        });

        $event = $this->getApp()->getApiManager()->getRouteEvent(Client::API_ENDPOINT_UPDATE_TABLE);
        $this->addEvent($event, function(){
            $this->getApp()->getHandleManager()->addHandle(Page::CLIENT_LIST);
        });

        $event = $this->getApp()->getApiManager()->getRouteEvent(Supplier::API_ENDPOINT_UPDATE_TABLE);
        $this->addEvent($event, function(){
            $this->getApp()->getHandleManager()->addHandle(Page::SUPPLIER_LIST);
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

        add_action( 'rest_api_init', function () {
            register_rest_route( 'myplugin/v1', '/author/(?P<id>\d+)', array(
                'methods'             => 'GET',
                'callback'            => 'my_awesome_func',
                'permission_callback' => function () {
                    return current_user_can( 'manage_options' );
                }
            ) );
        } );
        
        function my_awesome_func() {
            $user_id = get_current_user_id();
        
            return array(
                'status'  => true,
                'message' => 'Congrats! You successfully made an authenticated request to a protected endpoint',
                'user_id' => $user_id
            );
        }

        

    }
}