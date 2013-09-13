Minimum néscésaire
------------------

$serviceContainer["db.options"] = array(
    'driver'    => 'pdo_mysql',
    'host'      => 'localhost',
    'dbname'    => 'rnb',
    'user'      => 'root',
    'password'  => 'AdiPass14',
    'charset'   => 'utf8',
    );


    Pour la fonctionalité d'orm
    ---------------------------

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
--------------------------------------------------

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
