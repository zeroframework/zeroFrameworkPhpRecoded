<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 19/05/13
 * Time: 21:57
 * To change this template use File | Settings | File Templates.
 */

namespace controller;

use lib\Response;
use model\containerAwaire;

class minecraft extends containerAwaire {
	     public function toiAction()
	     {
		     return new Response(
			 $this
			 ->container
			 ->get("twig")
			 ->render("test.html.twig", array())
			 );
	     }
}