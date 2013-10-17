<?php
namespace service;

use Doctrine\ORM\Events;

class DoctrineEventTag {

    public function onReady($eventname, $app)
    {
        // Récupere le service container
        $container = $app->getServiceContainer();

        $doctrineEventManager = $container->get("db.event_manager");

        foreach($container->services as $servicename => $serviceparameters)
        {
            if(!empty($serviceparameters["tags"]))
            {
                foreach($serviceparameters["tags"] as $tag)
                {
                    if($tag["name"] == "doctrine.event")
                    {
                        $doctrineEventManager->addEventListener($tag["event"], $container->get($servicename));
                    }
                }
            }
        }

        $app->getEventManager()->notify("onDoctrineEventLoaded");
    }
}

?>