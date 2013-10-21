<?php

class twbsBundle
{
    public static function register(\app $app)
    {
        $container = $app->getServiceContainer();

        $container["twig.form.templates"] = array("form/twbsform.html.twig");

        //$container['twig.loader']->addLoader(new \Twig_Loader_Filesystem(__DIR__."/Resources/view"));

        if(!$container->has("twig.path")) $container["twig.path"] = array();

        $container["twig.path"] = array_merge($container["twig.path"], array(__DIR__."/Resources/view"));
    }
}

?>