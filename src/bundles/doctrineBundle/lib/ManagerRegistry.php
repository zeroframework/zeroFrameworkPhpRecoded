<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adibox
 * Date: 19/09/13
 * Time: 15:41
 * To change this template use File | Settings | File Templates.
 */

namespace lib;


use Doctrine\Common\Persistence\AbstractManagerRegistry;

class ManagerRegistry extends AbstractManagerRegistry {
    protected $container;

    protected function getService($name)
    {
        return $this->container[$name];
    }

    protected function resetService($name)
    {
        unset($this->container[$name]);
    }

    public function getAliasNamespace($alias)
    {
        throw new \BadMethodCallException("Namespace aliases not suported.");
    }

    public function setContainer(\serviceContainer $container)
    {
        $this->container = $container;
    }
}