<?php

namespace model;

use interfaces\containerAwaireInterface;

class containerAwaire implements containerAwaireInterface
{
	protected $container;

	public function setContainer($container)
	{
		$this->container = $container;
	}
	
	public function getContainer()
	{
		return $this->container;
	}
}

?>