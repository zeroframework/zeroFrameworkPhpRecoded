<?php

use Symfony\Component\Console\Application;

class consoleBundle
{
    public static function register(core $app)
    {
        $container = $app->getServiceContainer();

        $container->console = $container->share(function($c)
        {
            return new ConsoleApplicationZf();
        });

        $eventManager = $app->getEventManager();

        $eventManager->listenEvent("onReady", function($event, $app)
        {

            $container = $app->getServiceContainer();

            $console = $container->get("console");

            foreach($container->services as $servicename => $serviceparameters)
            {
                if(!empty($serviceparameters["tags"]))
                {
                    foreach($serviceparameters["tags"] as $tag)
                    {
                        if($tag["name"] == "kernel.command")
                        {
                            $console->add($container->get($servicename));
                        }
                    }
                }
            }
        });

    }
}