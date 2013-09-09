<?php

use Symfony\Component\HttpFoundation\Request;

class symfonyHttpFoundationBundle
{
    public static function register($app)
    {
        // Récupere le service container
        $container = $app->getServiceContainer();

        // Initialise le parametres services comme un tableau vide s'il n'existe pas sinon fussion un autre tableau à celui déjà existant
        $services = $app->getConf()->loadConfigurationFile("services", __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config");

        if(!$container->has("services")) $container->services = array();

        $container->services = array_merge($container->services, $services);

        $container["request"] = $container->share(function() {
            return Request::createFromGlobals();
        });
    }
}