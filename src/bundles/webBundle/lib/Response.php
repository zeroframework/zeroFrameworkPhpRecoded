<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 21/05/13
 * Time: 22:05
 * To change this template use File | Settings | File Templates.
 */

namespace lib;


class Response {
	   private $content;

	 public function __construct($content)
	 {
$this->content = $content;
	 }

	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	public function getContent()
	{
		  return $this->content;
	}

	public function render()
	{
		header("content-type: text/html;");

		echo $this->getContent();
	}
}