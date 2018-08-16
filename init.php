<?php
/*
Plugin Name: Impresario CRM
Plugin URI: http://www.dave-baker.com
Description: Impresario CRM System
Version: 0.0.1
Author: Dave Baker
Author URI: http://www.dave-baker.com
License: GPL

Copyright 2018  Dave Baker & Phil Sutton

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action('plugins_loaded', function(){
    $app = new DaveBaker\Core\App(
        "impresario",
        new \DaveBaker\Core\WP\Object\Manager(
            new \SuttonBaker\Impresario\WP\Config\Object
        )
    );
    $job = $app->getObjectManager()->get('\SuttonBaker\Impresario\Model\Db\Job');

    $job->setHorse("neighh!");

    var_dump($job->getHorse());

    $job2 = $app->getObjectManager()->get('\SuttonBaker\Impresario\Model\Db\Job');

    var_dump($job2->getHorse());

    $app2 = new DaveBaker\Core\App(
        "impresario2",
        new \DaveBaker\Core\WP\Object\Manager(
            new \SuttonBaker\Impresario\WP\Config\Object
        )
    );

    $job3 = $app2->getObjectManager()->get('\SuttonBaker\Impresario\Model\Db\Job');
    $job3->setHorse("Woo");

    var_dump($job3->getHorse());
    var_dump($job->getHorse());
    exit;
});


