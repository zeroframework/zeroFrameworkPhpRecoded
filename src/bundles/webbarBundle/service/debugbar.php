<?php
namespace service;

use lib\Response;

use \DOMDocument;

class debugbar
{
	public function onResponse($eventName, Response $response)
	{
		$htmlDoc = new DOMDocument();
		$htmlDoc->loadHTML($response->getContent());
		
		foreach($htmlDoc->getElementsByTagName("body") as $node)
		{
			$debubBarDom = $htmlDoc->createElement("div");
			
			$debubBarDom->appendChild($htmlDoc->createTextNode("mamamiya"));
			
			$node->appendChild($debubBarDom);
		}
		
		$response->setContent($htmlDoc->saveHTML());
	}

}

?>