<?php

use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Extension\SecurityExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Form\TwigRenderer;

class twigBundle
{
	public static function loadServices($app)
	{
		$container = $app->getServiceContainer();
	
	    // Initialise le parametres services comme un tableau vide s'il n'existe pas sinon fussion un autre tableau à celui déjà existant
	    $services = $app->getConf()->loadConfigurationFile("services", __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config");

	    if(!$container->has("services")) $container->services = array();

	    $container->services = array_merge($container->services, $services);
	}

	public static function register($core)
	{	
		twigBundle::loadServices($core);
	
		$app = $core->getServiceContainer();

        $eventManager = $core->getEventManager();
		
		$app["charset"] = "UTF-8";
	
		if(!$app->has("twig.options")) $app['twig.options'] = array();
        //$app['twig.form.templates'] = array('form_div_layout.html.twig');
        //$app['twig.path'] = array(APP_DIRECTORY."/html/");
        $app['twig.templates'] = array();

        $app['twig'] = $app->share(function ($app) use($core) {

            if(!$app->has("twig.path")) throw new \Exception("unknow twig.path");

            $app['twig.options'] = array_replace(
                array(
                    'charset'          => $app['charset'],
                    'debug'            => $app['debug'],
                    'strict_variables' => $app['debug'],
                ), $app['twig.options']
            );

            $twig = new \Twig_Environment($app['twig.loader'], $app['twig.options']);
            $twig->addGlobal('app', $app);
            //$twig->addExtension(new TwigCoreExtension());

            if ($app['debug']) {
                $twig->addExtension(new \Twig_Extension_Debug());
            }

            if (class_exists('Symfony\Bridge\Twig\Extension\RoutingExtension')) {
                if (isset($app['url_generator'])) {
                    $twig->addExtension(new RoutingExtension($app['url_generator']));
                }

                if (isset($app['translator'])) {
                    $twig->addExtension(new TranslationExtension($app['translator']));
                }

                if (isset($app['security'])) {
                    $twig->addExtension(new SecurityExtension($app['security']));
                }

                if (isset($app['form.factory'])) {
                    $app['twig.form.engine'] = $app->share(function ($app) {
                        return new TwigRendererEngine($app['twig.form.templates']);
                    });

                    $app['twig.form.renderer'] = $app->share(function ($app) {
                        return new TwigRenderer($app['twig.form.engine'], $app['form.csrf_provider']);
                    });

                    $twig->addExtension(new FormExtension($app['twig.form.renderer']));

                    // add loader for Symfony built-in form templates
                    $reflected = new \ReflectionClass('Symfony\Bridge\Twig\Extension\FormExtension');
                    $path = dirname($reflected->getFileName()).'/../Resources/views/Form';
                    $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($path));
                }
            }

            return $twig;
        });

        $app['twig.loader.filesystem'] = $app->share(function ($app) {
            return new \Twig_Loader_Filesystem($app['twig.path']);
        });

        $app['twig.loader.array'] = $app->share(function ($app) {
            return new \Twig_Loader_Array($app['twig.templates']);
        });

        $app['twig.loader'] = $app->share(function ($app) {
            return new \Twig_Loader_Chain(array(
                $app['twig.loader.array'],
                $app['twig.loader.filesystem'],
            ));
        });
		
	}
}

?>
