<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adibox
 * Date: 26/09/13
 * Time: 16:37
 * To change this template use File | Settings | File Templates.
 */

class gregwarImageBundle {
    public static function register($app)
    {
        // R�cupere le service container
        $container = $app->getServiceContainer();


        // Initialise le parametres services comme un tableau vide s'il n'existe pas sinon fussion un autre tableau � celui d�j� existant
        $services = $app->getConf()->loadConfigurationFile("services", __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config");

        if(!$container->has("services")) $container->services = array();

        $container->services = array_merge($container->services, $services);

        if(!$container->has("gregwarImage.config")) $container["gregwarImage.config"] = array();

        $config = $app->getConf()->loadConfigurationFile("config", __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config");

        $container["gregwarImage.config"] = array_merge($container["gregwarImage.config"], $config);
    }
}