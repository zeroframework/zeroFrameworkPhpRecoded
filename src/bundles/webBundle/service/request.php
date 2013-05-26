<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 05/05/13
 * Time: 17:01
 * To change this template use File | Settings | File Templates.
 */

namespace service;

use interfaces\event;

class request {

	public $vars;
	public $server;

	public function __construct()
	{
		$this->vars = new \ArrayObject();
		$this->server = new \ArrayObject();
	}

	// Quand l'évenement onReady est déclanché cetté méthode est appele
	// Le core passe cette méthode et on verfie grace à l'interface event que le core
	// Gere bien les events
	public function onReady($eventname, event $core)
	{
		// Ici on prépare la requete du serveur
		$this->prepareRequest();

		// Une fois le service request pre on notify de l'evenement qu'une requete HTTTP à été effectué
		$core->getEventManager()->notify("onRequest", $this);
	}

	private function prepareRequest()
	{	
		if(!$this->isWebRequest())
		{
		    return;
		}

		$this->vars = new \ArrayObject(array_merge($_GET, $_POST));

		$this->server = new \ArrayObject($_SERVER);
    }

	public function getUri()
	{
		list($uri) = explode("?", $this->server->offsetGet("REQUEST_URI"));
	
		return $uri;
	}
	
	private function isWebRequest()
	{
	    return isset($_GET);
	}

	public function isAjaxRequest()
	{

	}

	public function isGet()
	{

	}

	public function isPost()
	{

	}

	public function getIpClient()
	{
	    return $this->server->get("REMOTE_ADDR");
	}
}