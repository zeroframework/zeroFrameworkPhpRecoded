<?php


namespace Exception;

use Symfony\Component\HttpFoundation\Response;
use \Exception;

class ExceptionWithResponse extends Exception
{
	protected $response;

	public function __construct($message, Response $response = null)
	{
		parent::__construct($message);

		$this->response = $response;
	}

	public function isResponse()
	{
		return !empty($this->response);
	}

	public function getResponse()
	{
		return $this->response;
	}

	public function setResponse(Response $response) 
	{
		$this->response = $response;
		return $this;
	}
}

?>