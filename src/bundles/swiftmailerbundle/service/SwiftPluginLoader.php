<?php

namespace service;

class SwiftPluginLoader extends serviceHelper
{
    public function onReady($eventName, $core)
    {
        // Récupere le service container
        $container = $core->getServiceContainer();

        foreach($container->services as $servicename => $serviceparameters)
        {
            if(!empty($serviceparameters["tags"]))
            {
                foreach($serviceparameters["tags"] as $tag)
                {
                    if($tag["name"] == "swiftmailer.plugin")
                    {
                        $container->get("mailer.imediate")
                                ->registerPlugin($container->get($servicename));
                    }
                }
            }
        }

        $core->getEventManager()->notify("onSwiftMailerPluginLoaded");
    }
}