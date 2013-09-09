<?php

namespace service;

use interfaces\service;
use lib\Response;
use interfaces\containerAwaireInterface;

class httpKernel {

	private $logger;
	private $eventManager;
	private $container;

	public function __construct($logger, $eventManager, $container)
	{
		$this->logger = $logger;
		$this->eventManager = $eventManager;
		$this->container = $container;
	}


	// Cette méthode est déclanché par l'évenement onRequest provenant du service request
	public function onRequest($eventName, $request)
	{

		  // Le service request à toutes les varaibles serveur dans sa propriété server, comme c'est un arrayObject
		  // ON peut récupérer les infos de ce tableau par [] ou offsetGet
		  $pathinfo = @$request->getUri();
		  
		  if(empty($pathinfo)) return;

		  // Path info étant par éxemple égale  à /minecraft/toi pour http://localhost/index.php/minecraft/toi
		  // Car il contient tous ce qui est après index.php on ne veux pas du / du début
		  $uri = substr($pathinfo, 1);

		  // On log l'url dans le logger
		  $this->logger->info($uri);

		  // On décompose la chaine en tableau de différent élément grace au séparateur "/"
		  // POur minecraft/toi on aura array( [0] => "minecraft", [1] => "toi )
		  // http://fr2.php.net/manual/en/function.explode.php
		  $parametres = explode("/", $uri);

		  $filterController = new \ArrayObject(array(
			"method" => null,
			"controller" => null,
		  ));
		  
		  // On vérifie que le ttbleau est composé de deux élement minimum pour le controller et l'action
		  if(count($parametres) >= 2)
		  {
			   // On récupere $parameters[0] dans $controller et $parameters[1] dans $method grace à list;
			   // http://fr2.php.net/manual/en/function.list.php
			   list($controller, $method) = $parametres;

			   $filterController->offsetSet("controller", $controller);
			   $filterController->offsetSet("method", $method);
		  }
		  
		  if(
			!$filterController->offsetExists("controller")
			||
			!$filterController->offsetExists("method")
		  )
		  {
		    header("HTTP/1.0 404 Not Found");
			throw new \Exception("not controller matched by url found !");
		  }
			   
		  $this->eventManager->notify("httpkernel.controller", $filterController);
		  

			   // En mvc (modèle, vue , controlleur)

			   // Un cotnroller est une classe et la méthode ou action est une méthode de cette classe

			   // On mappe donc une url à l'appel d'une méthode (action) d'une classe (controleur)

			   // On crée un opbjet de réfléction de la classe qui permet de l'inspecter pour tous savoir sur elle
			   // Et également pour l'instancier, ou autre
			   // http://php.net/manual/fr/class.reflectionclass.php
			   $class = new \ReflectionClass("controller\\".$filterController->offsetGet("controller"));

			   // On demande de crée une nouvelel instance de controller\\$controller
			   // http://php.net/manual/en/reflectionclass.newinstance.php
			   $instance = $class->newInstance();
			   
			   if($instance instanceof containerAwaireInterface)
			   {
					$instance->setContainer($this->container);
			   }

			   // On récupere un objet d'introspection de la methodé $methodeAction (ReflectionMethod voir php.net)
			   // http://www.php.net/manual/en/reflectionclass.getmethod.php
			   // http://php.net/manual/en/class.reflectionmethod.php
			   $method = $class->getMethod($filterController->offsetGet("method")."Action");

			   // On invoque la méthode de la classe en demandant à la reflection de la méthode de le faire
			   // On spécifie l'instance de l'objet qu'on utilise et les argument qu'on passe
			   // Equivaut à $instance->method();
			   // http://php.net/manual/en/reflectionmethod.invokeargs.php
			   $return = $method->invokeArgs($instance, array());


			  $this->eventManager->notify("httpkernel.view", null);

			   // On affiche le retour de cette méthode
			   if(!$return instanceof Response) throw new \Exception("Controller doesn't return Response");
			  $this->eventManager->notify("httpkernel.response", $return);
				$return->render();
		  
	}

}