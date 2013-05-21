<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 25/04/13
 * Time: 22:04
 * To change this template use File | Settings | File Templates.
 */

class serviceContainer {
    private $services = array();

    public function __set($name , $value)
    {
        $this->services[$name] = $value;
    }

    public function has($name)
    {
        return isset($this->services[$name]);
    }

    public function __get($name)
    {
        if(!$this->has($name)) throw new Exception("service $name doesn't exist !");

        return (is_callable($this->services[$name])) ? $this->services[$name]($this) : $this->services[$name];
    }

    public function share(Closure $serviceHandle)
    {
         return function($c) use ($serviceHandle) {
             static $object;

             if(null === $object)
             {
                 $object = $serviceHandle($c);
             }

             return $object;
         };
    }

    public function get($name) { return $this->__get($name); }
}