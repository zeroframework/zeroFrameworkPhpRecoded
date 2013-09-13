<?php

namespace Exception;

use Symfony\Component\HttpFoundation\Response;

use \Exception;

class HumanException extends ExceptionWithResponse {

	private $realException;

	public function __construct($message, Exception $realException = null, Response $response = null)
	{
		$this->realException = $realException;

		$this->response = $response;

		parent::__construct($message, $response);
	}

	public function getRealException()
	{
		return $this->realException;
	}
}

?>