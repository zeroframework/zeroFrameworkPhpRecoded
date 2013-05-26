<?php

namespace service;

class routing
{
	private $container;
	private $request;
	
	public function __construct($container, $request)
	{
		$this->setContainer($container);
		$this->request = $request;
	}
	
	public function setContainer($container)
	{
		$this->container = $container;
	}
	
	public function getContainer()
	{
		return $this->container;
	}
	
	public function getRequest()
	{
		return $this->getContainer()->get("request");
	}

	public function getConfigRouting()
	{
		return $this->getContainer()->get("routing");
	}
	
	public function onDispatchController($eventName, \ArrayObject $config)
	{

		$pathinfo = @$this->request->getUri();

		if(empty($pathinfo)) return;
	
		foreach($this->getConfigRouting() as $routename => $route)
		{
			if($pathinfo == $route["path"])
			{
			    list($controller, $action) = explode(":", $route["controller"]);
				
				$config->offsetSet("controller", $controller);
				$config->offsetSet("method", $action);
			}
		}
	}
}

?>