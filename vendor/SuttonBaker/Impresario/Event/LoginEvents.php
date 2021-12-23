<?php

namespace SuttonBaker\Impresario\Event;
use DaveBaker\Core\Block\BlockInterface;
use DaveBaker\Core\Block\Html\Heading;
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
class LoginEvents extends \DaveBaker\Core\Base
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
        $this->addEvent('login_body_class', function($classes){
            $classes[] = 'bg-1';
            return $classes;
        });

    
        $this->addEvent('login_enqueue_scripts', function(){
            wp_register_style('fontawesomeimpresario', 'http://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
            wp_enqueue_style('fontawesomeimpresario');
            wp_register_style('mainimpresario', get_template_directory_uri() . '/assets/css/minimal.css', [], '1.0', 'all');
            wp_enqueue_style('mainimpresario');

            wp_register_script('loginimpresario', get_template_directory_uri() . '/assets/js/login.js', ['jquery']);
            wp_enqueue_script('loginimpresario');
        });

        $this->addEvent('login_header', function(){
            
            
        });

    }
}