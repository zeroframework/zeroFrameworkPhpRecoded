<?php

namespace Loader;

use interfaces\containerAwaireInterface;
use \Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class ZFRoutingLoader extends Loader
{
    private $confLoader;
    private $directory;

    public function __construct($confLoader, $directory)
    {
        $this->confLoader = $confLoader;
        $this->directory = $directory;
    }

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string $type     The resource type
     */
    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();

        $routes = $this->confLoader->loadConfigurationFile($resource, $this->directory);

        foreach($routes as $routename => $routeparam)
        {
            $collection->add($routename, new \Symfony\Component\Routing\Route($routeparam["path"], $routeparam["defaults"]));
        }

        return $collection;
    }

    /**
     * Returns true if this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string $type     The resource type
     *
     * @return Boolean true if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return $type === "advanced_extra";
    }

}