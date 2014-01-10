<?php

class symfonyRouterBundle
{
    public static function register(\app $app)
    {
        $container = $app->getServiceContainer();

        $scheme = ($container->has("request")) ? $container->get("request")->getScheme() : "http";

        $context = new \Symfony\Component\Routing\RequestContext($scheme."://".$container->get("domaine"));

        $container->routersymfony = new \Symfony\Component\Routing\Router(
            new Loader\ZFRoutingLoader($app->getConf(), APP_DIRECTORY."/Resources/config"),
            'routing',
            ($container->get("debug")) ? array() : array('cache_dir' => APP_DIRECTORY.'/cache'),
            $context
        );
    }
}