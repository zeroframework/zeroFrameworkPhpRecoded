<?php

use Symfony\Component\Form\Extension\Csrf\CsrfExtension;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\SessionCsrfProvider;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension as FormValidatorExtension;
use Symfony\Component\Form\Forms;

use lib\ManagerRegistry;

use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;

class symfonyFormBundle
{
    public static function register($app)
    {
        $app = $app->getServiceContainer();

        if (!class_exists('Locale') && !class_exists('Symfony\Component\Locale\Stub\StubLocale')) {
            throw new \RuntimeException('You must either install the PHP intl extension or the Symfony Locale Component to use the Form extension.');
        }

        if (!class_exists('Locale')) {
            $r = new \ReflectionClass('Symfony\Component\Locale\Stub\StubLocale');
            $path = dirname(dirname($r->getFilename())).'/Resources/stubs';

            require_once $path.'/functions.php';
            require_once $path.'/Collator.php';
            require_once $path.'/IntlDateFormatter.php';
            require_once $path.'/Locale.php';
            require_once $path.'/NumberFormatter.php';
        }

        $app['form.secret'] = md5(__DIR__);

        if($app->has(""))
        {
            $app['form.type.extensions'] = $app->share(function ($app) {
                return array();
            });
        }

        //if($app)

        $app['form.type.extensions'] = $app->share(function ($app) {
            return array();
        });

        $app['form.type.guessers'] = $app->share(function ($app) {
            return array();
        });

        $app['form.extensions'] = $app->share(function ($app) {
            $extensions = array(
                new CsrfExtension($app['form.csrf_provider']),
                new HttpFoundationExtension(),
            );

            if (isset($app['validator'])) {
                $extensions[] = new FormValidatorExtension($app['validator']);

                if (isset($app['translator'])) {
                    $r = new \ReflectionClass('Symfony\Component\Form\Form');
                    $app['translator']->addResource('xliff', dirname($r->getFilename()).'/Resources/translations/validators.'.$app['locale'].'.xlf', $app['locale'], 'validators');
                }
            }

            return $extensions;
        });

        $app['form.factory'] = $app->share(function ($container) {
                $factory = Forms::createFormFactoryBuilder();

                foreach($container->services as $servicename => $serviceparameters)
                {
                    if(!empty($serviceparameters["tags"]))
                    {
                        foreach($serviceparameters["tags"] as $tag)
                        {
                            if($tag["name"] == "form.type")
                            {
                                $factory->addType($container->get($servicename));
                            }
                            elseif($tag["name"] == "form.type_extension")
                            {
                                $factory->addTypeExtension($container->get($servicename));
                            }
                            elseif($tag["name"] == "form.extension")
                            {
                                $factory->addExtension($container->get($servicename));
                            }
                            elseif($tag["name"] == "form.type_guesser")
                            {
                                $factory->addTypeGuesser($container->get($servicename));
                            }
                        }
                    }
                }

                $container->get("eventmanager")->notify("onExtensionFormLoaded");

                return $factory
                    ->addExtensions($container['form.extensions'])
                    ->addTypeExtensions($container['form.type.extensions'])
                    ->addTypeGuessers($container['form.type.guessers'])
                    ->getFormFactory();
            });

            $app['form.csrf_provider'] = $app->share(function ($app) {
                if (isset($app['session'])) {
                    return new SessionCsrfProvider($app['session'], $app['form.secret']);
                }

                return new DefaultCsrfProvider($app['form.secret']);
            });

            if($app->has("form.factory"))
            {
                self::loadDoctrineFormExtension($app);
            }
    }


    private static function loadDoctrineFormExtension($app)
    {
        $app['form.extensions'] = $app->share($app->extend('form.extensions', function ($extensions, $app) {
            $managerRegistry = new ManagerRegistry(null, array(), array('db.orm.em'), null, null, (!$app->has("doctrine_orm.proxies_namespace")) ? '\Doctrine\ORM\Proxy\Proxy' : $app['doctrine_orm.proxies_namespace']);
            $managerRegistry->setContainer($app);
            $extensions[] = new DoctrineOrmExtension($managerRegistry);

            return $extensions;
        }));
    }
}
?>