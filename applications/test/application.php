<?php

class application {
	public static function register($app)
	{
		// Récupere le service container
	    $container = $app->getServiceContainer();

	    // Initialise le parametres services comme un tableau vide s'il n'existe pas sinon fussion un autre tableau à celui déjà existant
	    $routing = $app->getConf()->loadConfigurationFile("routing", __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config");

	    if(!$container->has("routing")) $container->routing = array();

	    $container->routing = array_merge($container->routing, $routing);
	}
}

?>