<?php

class decodaBundle
{
    public static function loadServices($app)
    {
        $container = $app->getServiceContainer();

        // Initialise le parametres services comme un tableau vide s'il n'existe pas sinon fussion un autre tableau à celui déjà existant
        $services = $app->getConf()->loadConfigurationFile("services", __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config");

        if(!$container->has("services")) $container->services = array();

        $container->services = array_merge($container->services, $services);
    }

    public static function register(\core $core)
    {
        self::loadServices($core);

        $container = $core->getServiceContainer();

        $container->decoda = $container->share(function($c)
        {
           $instance = new \Decoda\Decoda();

           if($c->has("twig")) $instance->setEngine($c["decodatwigengine"]);

            return $instance;
        });

        $container->decodatwigengine = $container->share(function($c)
        {
           return new Engine\TwigEngine($c);
        });

        $eventManager = $core->getEventManager();

        $eventManager->listenEvent("onReady", function($eventName, $core)
        {
            // Récupere le service container
            $container = $core->getServiceContainer();

            foreach($container->services as $servicename => $serviceparameters)
            {
                if(!empty($serviceparameters["tags"]))
                {
                    foreach($serviceparameters["tags"] as $tag)
                    {
                        if($tag["name"] == "decoda.filter")
                        {
                            $container->get("decoda")
                                    ->addFilter($container->get($servicename));

                        }
                    }
                }
            }

            $core->getEventManager()->notify("onFilterDecodaLoaded");
        });
    }
}