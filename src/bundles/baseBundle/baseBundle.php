<?php

class baseBundle {

    /**
     * use         "psr/log" : "dev-master"
     * Important pour la norme psr sur les application de log
     */

    // Enregistre le bundle
    public static function register($app)
    {
        // Récupere le service container
        $container = $app->getServiceContainer();

        // Initialise le parametres services comme un tableau vide s'il n'existe pas sinon fussion un autre tableau à celui déjà existant
        $services = $app->getConf()->loadConfigurationFile("services", __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config");

	    if(!$container->has("services")) $container->services = array();

	    $container->services = array_merge($container->services, $services);

	    $container->eventmanager = $app->getEventManager();

        $container->eventmanager->listenEvent("onReady", function() use ($container)
        {
            if($container->eventmanager instanceof \Psr\Log\LoggerAwareInterface)
            {
                $container->eventmanager->setLogger($container->logger);
            }
        });

        $container->kernel = $app;
    }
}