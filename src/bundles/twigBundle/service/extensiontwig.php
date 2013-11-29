<?php

namespace service;

class extensiontwig 
{
	public function onReady($eventname, $app)
	{
		// Récupere le service container
	    $container = $app->getServiceContainer();

		foreach($container->services as $servicename => $serviceparameters)
		{
		    if(!empty($serviceparameters["tags"]))
		    {
			    foreach($serviceparameters["tags"] as $tag)
			    {
				    if($tag["name"] == "twig.extension")
				    {
                        $extensionname = $container->get($servicename)->getName();

                        if(!$container->get("twig")->hasExtension($extensionname))
                        {
                            $container->get("twig")
                                ->addExtension($container->get($servicename));
                        }
				    }
			    }
		    }
		}

        $app->getEventManager()->notify("onExtensionTwigLoaded");
	}
}
