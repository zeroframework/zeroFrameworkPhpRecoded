<?php

class symfonyRouterBundle
{
    public static function register(\app $app)
    {
        $container = $app->getServiceContainer();

        $context = new \Symfony\Component\Routing\RequestContext((isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : $container->get("baseurl")));

        $container->routersymfony = new \Symfony\Component\Routing\Router(
            new Loader\ZFRoutingLoader($app->getConf(), APP_DIRECTORY."/Resources/config"),
            'routing',
            ($container->get("debug")) ? array() : array('cache_dir' => APP_DIRECTORY.'/cache'),
            $context
        );
    }
}