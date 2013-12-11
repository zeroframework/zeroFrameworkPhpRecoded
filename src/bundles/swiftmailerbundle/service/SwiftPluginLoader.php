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
                        $mailer = (isset($tag["mailer"])) ? $container->get($tag["mailer"]) : $container->get("mailer.imediate");

                        $mailer->registerPlugin($container->get($servicename));
                    }
                }
            }
        }

        $core->getEventManager()->notify("onSwiftMailerPluginLoaded");
    }
}