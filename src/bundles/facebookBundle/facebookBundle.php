<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adibox
 * Date: 26/09/13
 * Time: 12:47
 * To change this template use File | Settings | File Templates.
 */

class facebookBundle {
    public static function register(\interfaces\core $app)
    {
        // Récupere le service container
        $container = $app->getServiceContainer();

        // Initialise le parametres services comme un tableau vide s'il n'existe pas sinon fussion un autre tableau à celui déjà existant
        $services = $app->getConf()->loadConfigurationFile("services", __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config");

        if(!$container->has("services")) $container->services = array();

        $container->services = array_merge($container->services, $services);

        $container->eventmanager = $app->getEventManager();
    }
}