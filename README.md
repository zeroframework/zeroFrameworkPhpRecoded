zeroFrameworkPhpRecoded
=======================

version du zeroframework réecrite pour la formation d'une personne au dév de a a z d'un framework light


Example de configuration
------------------------

application

<?php

use \Doctrine\Common\Cache\ApcCache;
use \Doctrine\Common\Cache\ArrayCache;


class application {
	public static function register($app)
	{

		// Récupere le service container
	    $container = $app->getServiceContainer();

	    // Initialise le parametres services comme un tableau vide s'il n'existe pas sinon fussion un autre tableau à celui déjà existant
	    $routing = $app->getConf()->loadConfigurationFile("routing", __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config");

	    if(!$container->has("routing")) $container->routing = array();

	    $container->routing = array_merge($container->routing, $routing);

        $serviceContainer = $app->getServiceContainer();

        $configurationDirectory = __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config";

        $config = $app->getConf()->loadConfigurationFile("config_".$app->getName(), $configurationDirectory);

        $importConfigurations = $config["import_configurations"];

        foreach($importConfigurations as $file)
        {
            $serviceContainer->merge($app->getConf()->loadConfigurationFile($file, $configurationDirectory));
        }

        $serviceContainer->merge($config);

        $serviceContainer = $app->getServiceContainer();

        $serviceContainer["debug"] = true;

        $serviceContainer["db.options"] = array(
            'driver'    => '',
            'host'      => '',
            'dbname'    => '',
            'user'      => '',
            'password'  => '',
            'charset'   => '',
        );

        $serviceContainer["mailer.options"] = array(
            "server" => "",
            "port" => 465,
            "security" => "ssl",
            "username" => "",
            "password" => "",
        );

        $serviceContainer->merge(array(
            'db.orm.proxies_dir'           => __DIR__ . '/cache/doctrine/proxy',
            'db.orm.proxies_namespace'     => 'DoctrineProxy',
            'db.orm.cache'                 =>
            !$serviceContainer["debug"] && extension_loaded('apc') ? new ApcCache() : new ArrayCache(),
            'db.orm.auto_generate_proxies' => true,
            'db.orm.entities'              => array(array(
                'type'      => 'annotation',       // entity definition
                'path'      => __DIR__."/Entity",   // path to your entity classes
                'namespace' => 'Entity', // your classes namespace
            )),
        ));

        $config = $app->getConf()->loadConfigurationFile("config", __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config");

        $serviceContainer->merge($config);
?>

config application
------------------

{
    "debug" : true,
    "facebook.config" :
    {
        "appId" : "",
        "secret" : ""
    },
    "db.options" :
    {
        "driver"    : "pdo_mysql",
        "host"      : "localhost",
        "dbname"    : "",
        "user"      : "",
        "password"  : "",
        "charset"   : "utf8"
    },
    "mailer.options" :
    {
        "server"  : "smtp.gmail.com",
        "port"     : 465,
        "security" : "ssl",
        "username" : "",
        "password" : ""
    }
}

bootloader

<?php


//include("../zf/vendor/autoload.php");
include(__DIR__."/../zf/core.php");
include(__DIR__."/../zf/lib/autoloader.php");

/**
 * @author : John <jpasqualini75@gmail.com>
 * @license : gpl
 * https://github.com/onetest/zeroFrameworkPhpRecoded
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

define("APP_DIRECTORY", __DIR__."/app");

// On active l'autolaoder
$autoloader = autoloader::register();

// On ajoute deux répertoire ou seront chercher les classes par l'autoloader
$autoloader->addBaseDirectory(core::_getCoreDirectory());

$autoloader->addBaseDirectory(__DIR__."/app");

// Je declare les différent modules (bundle ou packetage en francais)

// Le bundle de base du framework
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/baseBundle");

// Le bundle permettant de gerer des requete et réponse provenement d'un serveur web
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/webBundle");

// Le bundle twigBundle
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/twigBundle");

// Le bundle twigBundle
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/codeIgniterBundle");

// Le bundle symfonyhttpfoundation
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/symfonyHttpFoundationBundle");

// Le bundle doctrine
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/doctrineBundle");

// Le bundle form
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/symfonyFormBundle");

// Le bundle extension twig bundle
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/twigWebExtensionBundle");

// Le bundle routingBundle
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/routingBundle");

// Le bundle webBar
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/webbarBundle");

// Le bundle swiftmailer
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/swiftmailerbundle");

// Le bundle facebook
$autoloader->addBaseDirectory(core::_getCoreDirectory()."/src/bundles/facebookBundle");

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

    private static $instance;

    public function __construct($name)
    {
	    self::$instance = $this;
        $this->setName($name);
    }

    public static function getInstance()
    {
        return self::$instance;
    }

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

$app = new app("dev");

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


baseBundle::register($app);
/**
webBundle::register($app);
*/
doctrineBundle::register($app);
symfonyFormBundle::register($app);
twigBundle::register($app);
twigWebExtensionBundle::register($app);
codeIgniterBundle::register($app);
symfonyHttpFoundationBundle::register($app);
/**
routingBundle::register($app);
**/
webbarBundle::register($app);

swiftmailerbundle::register($app);

application::register($app);

facebookBundle::register($app);

// Schema de conception
/**
run -> onReady -> Request -> onRequest -> httpKernel -> onDispatchController -> Routing -> httpKernel -> new constroller() -> method($paramurl1, $paramurl2, etc..) -> new Response() -> httpKernel -> response -> getContent() -> echo -> web browser
 *
 */

try {
    $app->run();
}
catch(\Exception $e)
{
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "Exception Catch : ".$e->getMessage();
	echo $e->getTraceAsString ();
}

?>
=======
$serviceContainer["db.options"] = array(
    'driver'    => 'pdo_mysql',
    'host'      => 'localhost',
    'dbname'    => '',
    'user'      => '',
    'password'  => '',
    'charset'   => 'utf8',
    );


    Pour la fonctionalité d'orm
    ---------------------------
    
    $serviceContainer->merge(array(
    'db.orm.proxies_dir'           => __DIR__ . '/cache/doctrine/proxy',
    'db.orm.proxies_namespace'     => 'DoctrineProxy',
    'db.orm.cache'                 =>
    !$serviceContainer["debug"] && extension_loaded('apc') ? new ApcCache() : new ArrayCache(),
    'db.orm.auto_generate_proxies' => true,
    'db.orm.entities'              => array(array(
    'type'      => 'annotation',       // entity definition
    'path'      => __DIR__ . '/src',   // path to your entity classes
    'namespace' => 'MyWebsite\Entity', // your classes namespace
    )),
    ));
    
Example de configuration pour la console doctrine
--------------------------------------------------

    require __DIR__.'/../vendor/autoload.php';
    include __DIR__."/../zfboot.php";

    use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
    use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
    use Doctrine\ORM\Tools\Console\ConsoleRunner;
    use Symfony\Component\Console\Helper\HelperSet;

    $container = $app->getServiceContainer();

    $helperSet = new HelperSet(array(
    "db" => new ConnectionHelper($container["db.orm.em"]->getConnection()),
    "em" => new EntityManagerHelper($container["db.orm.em"])
    ));

    ConsoleRunner::run($helperSet);

