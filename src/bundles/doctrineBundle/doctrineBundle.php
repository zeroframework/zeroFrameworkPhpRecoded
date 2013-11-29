<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adibox
 * Date: 09/09/13
 * Time: 15:45
 * To change this template use File | Settings | File Templates.
 */

use Symfony\Bridge\Doctrine\Logger\DbalLogger;


use \Doctrine\DBAL\Configuration as DBALConfiguration,
    \Doctrine\DBAL\DriverManager;

use \Doctrine\ORM\Configuration as ORMConfiguration,
    \Doctrine\ORM\Mapping\Driver\AnnotationDriver,
    \Doctrine\ORM\Mapping\Driver\YamlDriver,
    \Doctrine\ORM\Mapping\Driver\XmlDriver,
    \Doctrine\ORM\EntityManager;

use \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain,
    \Doctrine\Common\Annotations\AnnotationReader,
    \Doctrine\Common\Cache\ArrayCache,
    \Doctrine\Common\Cache\ApcCache,
    \Doctrine\Common\EventManager;

use \Doctrine\Common\Annotations\AnnotationRegistry;

class doctrineBundle
{
    /**
     * Example de configuration
     *
     * Minimum néscésaire
     *
     * "symfony/doctrine-bridge": "2.4.*@dev"
     *  "doctrine/orm": ">= 2.3.0, < 2.4.0-beta",
     * "doctrine/common": ">= 2.3.0, < 2.4.0-beta",
     * "doctrine/dbal": ">= 2.3.0, < 2.4.0-beta",
     *
     * $serviceContainer["db.options"] = array(
    'driver'    => 'pdo_mysql',
    'host'      => 'localhost',
    'dbname'    => 'rnb',
    'user'      => 'root',
    'password'  => 'AdiPass14',
    'charset'   => 'utf8',
    );

    // Pour la fonctionalité d'orm
    $serviceContainer->merge(array(
    'db.orm.proxies_dir'           => __DIR__ . '/cache/doctrine/proxy',
    'db.orm.proxies_namespace'     => 'DoctrineProxy',
    'db.orm.cache'                 =>
    !$serviceContainer["debug"] && extension_loaded('apc') ? new ApcCache() : new ArrayCache(),
    'db.orm.auto_generate_proxies' => true,
    'db.orm.entities'              => array(array(
    'type'      => 'annotation',       // entity definition
    'path'      => __DIR__ . '/src',   // path to your entity classes
    'namespace' => 'MyWebsite\Entity', // your classes namespace
    )),
    ));


     Example de configuration pour la console doctrine

    require __DIR__.'/../vendor/autoload.php';
    include __DIR__."/../zfboot.php";

    use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
    use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
    use Doctrine\ORM\Tools\Console\ConsoleRunner;
    use Symfony\Component\Console\Helper\HelperSet;

    $container = $app->getServiceContainer();

    $helperSet = new HelperSet(array(
    "db" => new ConnectionHelper($container["db.orm.em"]->getConnection()),
    "em" => new EntityManagerHelper($container["db.orm.em"])
    ));

    ConsoleRunner::run($helperSet);
     */

    public static function loadServices($app)
    {
        $container = $app->getServiceContainer();

        // Initialise le parametres services comme un tableau vide s'il n'existe pas sinon fussion un autre tableau à celui déjà existant
        $services = $app->getConf()->loadConfigurationFile("services", __DIR__.DIRECTORY_SEPARATOR."Resources".DIRECTORY_SEPARATOR."config");

        if(!$container->has("services")) $container->services = array();

        $container->services = array_merge($container->services, $services);
    }


    public static function register($app)
    {

      self::loadServices($app);

      $app = $app->getServiceContainer();

      $app['db.default_options'] = array(
          'driver'   => 'pdo_mysql',
          'dbname'   => null,
          'host'     => 'localhost',
          'user'     => 'root',
          'password' => null,
          'port'     => null,
      );

      $app['dbs.options.initializer'] = $app->protect(function () use ($app) {
          static $initialized = false;

          if ($initialized) {
              return;
          }

          $initialized = true;

          if (!isset($app['dbs.options'])) {
              $app['dbs.options'] = array('default' => isset($app['db.options']) ? $app['db.options'] : array());
          }

          $tmp = $app['dbs.options'];
          foreach ($tmp as $name => &$options) {
              $options = array_replace($app['db.default_options'], $options);

              if (!isset($app['dbs.default'])) {
                  $app['dbs.default'] = $name;
              }
          }
          $app['dbs.options'] = $tmp;
      });

      $app['dbs'] = $app->share(function ($app) {
          $app['dbs.options.initializer']();

          $dbs = new serviceContainer();
          foreach ($app['dbs.options'] as $name => $options) {
              if ($app['dbs.default'] === $name) {
                  // we use shortcuts here in case the default has been overridden
                  $config = $app['db.config'];
                  $manager = $app['db.event_manager'];
              } else {
                  $config = $app['dbs.config'][$name];
                  $manager = $app['dbs.event_manager'][$name];
              }

              $dbs[$name] = $dbs->share(function ($dbs) use ($options, $config, $manager) {
                  return DriverManager::getConnection($options, $config, $manager);
              });
          }

          return $dbs;
      });

      $app['dbs.config'] = $app->share(function ($app) {
          $app['dbs.options.initializer']();

          $configs = new serviceContainer();
          foreach ($app['dbs.options'] as $name => $options) {
              $configs[$name] = new DBALConfiguration();

              if (isset($app['logger']) && class_exists('Symfony\Bridge\Doctrine\Logger\DbalLogger')) {
                  $configs[$name]->setSQLLogger(new DbalLogger($app['logger'], isset($app['stopwatch']) ? $app['stopwatch'] : null));
              }
          }

          return $configs;
      });

      $app['dbs.event_manager'] = $app->share(function ($app) {
          $app['dbs.options.initializer']();

          $managers = new serviceContainer();
          foreach ($app['dbs.options'] as $name => $options) {
              $managers[$name] = new EventManager();
          }

          return $managers;
      });

      // shortcuts for the "first" DB
      $app['db'] = $app->share(function ($app) {
          $dbs = $app['dbs'];

          return $dbs[$app['dbs.default']];
      });

      $app['db.config'] = $app->share(function ($app) {
          $dbs = $app['dbs.config'];

          return $dbs[$app['dbs.default']];
      });

      $app['db.event_manager'] = $app->share(function ($app) {
          $dbs = $app['dbs.event_manager'];

          return $dbs[$app['dbs.default']];
      });

      self::loadDoctrineConfiguration($app);
      self::setOrmDefaults($app);
      self::loadDoctrineOrm($app);
  }

    private static function loadDoctrineOrm(serviceContainer $app)
    {
        $app['db.orm.em'] = $app->share(function() use($app) {
            return EntityManager::create($app['db'], $app['db.orm.config']);
        });
    }

    private static function setOrmDefaults(serviceContainer $app)
    {
        $defaults = array(
            'entities' => array(
                array(
                    'type' => 'annotation',
                    'path' => 'Entity',
                    'namespace' => 'Entity',
                )
            ),

            'proxies_dir'           => 'cache/doctrine/Proxy',
            'proxies_namespace'     => 'DoctrineProxy',
            'auto_generate_proxies' => true,
            'cache'                 => new ArrayCache,
        );

        foreach ($defaults as $key => $value) {
            if (!isset($app['db.orm.' . $key])) {
                $app['db.orm.'.$key] = $value;
            }
        }
    }

    public static function loadDoctrineConfiguration(serviceContainer $app)
    {
        $app['db.orm.config'] = $app->share(function() use($app) {

            $cache = $app['db.orm.cache'];

            $config = new ORMConfiguration;
            $config->setMetadataCacheImpl($cache);
            $config->setQueryCacheImpl($cache);
            $config->setResultCacheImpl($cache);

            $chain = new MappingDriverChain;

            foreach((array) $app['db.orm.entities'] as $entity) {
                switch($entity['type']) {
                    case 'default':
                    case 'annotation':
                        $driver = $config->newDefaultAnnotationDriver((array)$entity['path']);
                        $chain->addDriver($driver, $entity['namespace']);
                        break;
                    case 'yml':
                        $driver = new YamlDriver((array)$entity['path']);
                        $driver->setFileExtension('.yml');
                        $chain->addDriver($driver, $entity['namespace']);
                        break;
                    case 'xml':
                        $driver = new XmlDriver((array)$entity['path'], $entity['namespace']);
                        $driver->setFileExtension('.xml');
                        $chain->addDriver($driver, $entity['namespace']);
                        break;
                    default:
                        throw new \InvalidArgumentException(sprintf('"%s" is not a recognized driver', $entity['type']));
                        break;
                }
            }

            $config->setMetadataDriverImpl($chain);

            $config->setProxyDir($app['db.orm.proxies_dir']);
            $config->setProxyNamespace($app['db.orm.proxies_namespace']);
            $config->setAutoGenerateProxyClasses($app['db.orm.auto_generate_proxies']);

            return $config;
        });
    }
}

?>