<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 21/05/13
 * Time: 22:16
 * To change this template use File | Settings | File Templates.
 */

namespace service;

use lib\Response;

class debug {
	      public function onKernelResponse(Response $response)
	      {
		        $response->setContent($response->getContent() + "hack response");
	      }
}