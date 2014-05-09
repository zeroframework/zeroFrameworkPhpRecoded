<?php

class symfonyRouterBundle
{
    public static function register(\app $app)
    {
        $container = $app->getServiceContainer();

        $context = new \Symfony\Component\Routing\RequestContext();

        if($container->has("request"))
        {
            $request = $container->get("request");

            $context->setBaseUrl($request->getScheme()."://".$container->get("domaine"));
            $context->setMethod($request->getMethod());
        }
        else
        {
            $context->setBaseUrl("http://".$container->get("domaine"));
        }

        $container->routersymfony = new \Symfony\Component\Routing\Router(
            new Loader\ZFRoutingLoader($app->getConf(), APP_DIRECTORY."/Resources/config"),
            'routing',
            ($container->get("debug")) ? array() : array('cache_dir' => $container->get("routing.config")["cache_dir"]),
            $context
        );
    }
}