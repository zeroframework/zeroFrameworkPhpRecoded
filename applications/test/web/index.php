<?php
include("../../../vendor/autoload.php");
include("../../../core.php");
include("../../../lib/autoloader.php");

/**
 * @author : Gauthier
 * @license : gpl
 * Composition d'un framework lite
 * - Bootloader (Le fichier qui demare le framework et qui appelle le core celui-ci donc)
 * - Core (Le minimum qu'a besoin le framework pour fonctionner meme s'il n'a aucune fonctionalité)
 * - Autoloader (Le chargeur de classe automatique au besoin lors de l'utilisation de celle ci sans recourir a include)
 * - Event (La gestion evenementiel qui permet d'ecouter certain evenement et d'effectuer des actions en consequence ex : OnCoreBoot)
 * - Service (Classe a instance unique accesible via un nom cours et pouvant declarer des dependance vie a vie d'autre service ex : HttpKernel)
 * - Error (C'est un service qui ecoute tous les event et qui ecrit des log en consequence, les exeception genere des evenement OnException )
 * - Template (Service lié a un moteur de template permettant de séparer la vue du code du controlleur)
 * - Routintg (Service qui s'occupe de liée une adresse a l'apelle d'une fonction d'un controlleur, un controlleur est une classe)
 * - LoadConf (Service qui se charge de charger un fichier de configuration on lui demande une conf et lui la trouve quelque soit la source fichier, bdd, memoire et le format)t'a pas les
 * - Trait (Sorte de hook permettant d'agrementer le core de fonctionalité indispensable en les separant du core meme, le core est donc tres leger et on peut ajouter des brique au core)
 * - Orm (Service qui lit le framework a l'orm qui permet de gerer la base en tant qu'objet)
 */

define("APP_DIRECTORY", __DIR__);

// On active l'autolaoder
$autoloader = autoloader::register();

// On ajoute deux répertoire ou seront chercher les classes par l'autoloader
$autoloader->addBaseDirectory(core::_getCoreDirectory());
$autoloader->addBaseDirectory(__DIR__."/..");

// Je declare les différent modules (bundle ou packetage en francais)

// Le bundle de base du framework
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/baseBundle");

// Le bundle permettant de gerer des requete et réponse provenement d'un serveur web
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/webBundle");

// Le bundle twigBundle
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/twigBundle");

// Le bundle extension twig bundle
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/twigWebExtensionBundle");

// Le bundle routingBundle
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/routingBundle");

// Le bundle webBar
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/webbarBundle");

/**
 * Class app
 * Classe principale de l'application , c'est par la qu'on passe l'ors du chargement de cette page
 * un peu comme la fonction main en c++ c'est la classe de démarage de l'application
 */
class app Extends Core implements interfaces\event, interfaces\core {
    use bridge\test;
    use bridge\event;
    use bridge\conf;
    use bridge\serviceContainer;

    public function run()
    {
	    $container = $this->getServiceContainer();
		$services = $container->get("services");

	    foreach($services as $servicename => $serviceparameters)
	    {
			$container->$servicename = function($c) use ($servicename, $serviceparameters)
		    {
			    $class = new ReflectionClass($serviceparameters["class"]);

			    $contructor = $class->getConstructor();

			    $parameters = array();

			    foreach($serviceparameters["parameters"]  as $parameter)
			    {
				    if(preg_match("/@(?P<name>[A-Za-z-._]+)/i", $parameter, $matches))
				    {
					    switch($matches["name"])
						{
						    case "service_container":
								$parameters[] = $c;
							break;

							default:
								$parameters[] = $c->get($matches["name"]);
							break;
						}

				    }
			    }
				
			    return $class->newInstanceArgs($parameters);
		    };
			
			$container->$servicename = $container->share($container->raw($servicename));
	    }

	    foreach($services as $servicename => $serviceparameters)
	    {
		    if(!empty($serviceparameters["tags"]))
		    {
			    foreach($serviceparameters["tags"] as $tag)
			    {
				    if($tag["name"] == "kernel.event")
				    {
					    $this
						    ->getEventManager()
						    ->listenEvent($tag["event"], array($container->get($servicename), $tag["method"]));
				    }
			    }
		    }
	    }

        $this->getEventManager()->notify("onReady", $this);
    }
}

$app = new app();

// On va charger quelque libraire indépante dont l'autoloader
// On récupere la liste les librairies a charger dans un tableau
// La function glob en php retourne dans un tableau la liste des fichiers qui correction a l'expression de recherche ici * = n'importe quoid
// On recupere donc tous les fichiers php du répértoire lib dans le core directory
$libs_directory = $app->getCoreDirectory()."/lib";
$libs_files = glob($libs_directory."/*.php");

// Parcour un tableau et appele la fonction anonyme passé en deuxieme parametre sur chaque element du tableau
array_walk($libs_files, function($lib)
{
    // Ici on inclu dynamiquement toutes les libs .php du dossier lib :) (pratique)
    require_once($lib);
});

application::register($app);
baseBundle::register($app);
webBundle::register($app);
twigBundle::register($app);
twigWebExtensionBundle::register($app);
routingBundle::register($app);
webbarBundle::register($app);

// Schema de conception
/**
run -> onReady -> Request -> onRequest -> httpKernel -> onDispatchController -> Routing -> httpKernel -> new constroller() -> method($paramurl1, $paramurl2, etc..) -> new Response() -> httpKernel -> response -> getContent() -> echo -> web browser
 *
 */

try {
    $app->run();
}
catch(Exception $e)
{
    echo "Exception Catch : ".$e->getMessage();
	echo $e->getTraceAsString ();
}

?>