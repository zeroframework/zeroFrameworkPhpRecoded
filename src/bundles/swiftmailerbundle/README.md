Configuration
=============

Gmail example
-------------

        $serviceContainer["mailer.options"] = array(
            "server" => "localhost",
            "port" => 456,
            "security" => "ssl",
            "username" => "username",
            "password" => "password",
        );


Example for send mail with file spool
=====================================

<?php
require __DIR__.'/../vendor/autoload.php';
include __DIR__."/../zfboot.php";

$serviceContainer = $app->getServiceContainer();

if($container->get("mailer.initialized"))
{
    $container
        ->get("swiftmailer.spooltransport")
        ->getSpool()
        ->flushQueue($container->get("swiftmailer.transport"))
    ;
}

flush spool