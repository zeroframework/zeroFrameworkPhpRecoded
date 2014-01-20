<?php
use Symfony\Component\HttpKernel\HttpCache\HttpCache;

use Symfony\Component\HttpKernel\HttpCache\Esi;

use Symfony\Component\HttpKernel\HttpCache\Store;

class symfonyHttpCacheBundle
{
    public static function register($app)
    {
        $app = $app->getServiceContainer();

        $app['http_cache'] = $app->share(function ($app) {
            return new HttpCache($app['kernel'], $app['http_cache.store'], $app['http_cache.esi'], $app['http_cache.options']);
        });

        $app['http_cache.esi'] = $app->share(function ($app) {
            return new Esi();
        });

        $app['http_cache.store'] = $app->share(function ($app) {
            return new Store($app['http_cache.cache_dir']);
        });

        $app['http_cache.options'] = array();
    }
}
?>