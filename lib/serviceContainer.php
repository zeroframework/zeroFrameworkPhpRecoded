<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 25/04/13
 * Time: 22:04
 * To change this template use File | Settings | File Templates.
 */

trait littleEvent {

    private $listener = null;

    public function on(\Closure $listener)
    {
        $this->listener = $listener;
    }

    public function getListener()
    {
        return $this->listener;
    }

    public function hasListener()
    {
        return ($this->listener !== null && $this->listener instanceof \Closure);
    }

    public function notify()
    {
        if(!$this->hasListener()) return;

        call_user_func_array($this->listener, func_get_args());
    }
}

class serviceContainer implements ArrayAccess {

    use littleEvent;

    const EVENT_SERVICE_LOADED = 1;

    private $services = array();

    public function __set($name , $value)
    {
        $this->services[$name] = $value;
    }

    public function has($name)
    {
        return isset($this->services[$name]);
    }
	
	public function offsetUnset($name)
	{
		unset($this->services[$name]);
	}
	
	public function offsetExists($name)
	{
		return isset($this->services[$name]);
	}
	
	public function offsetGet($name)
	{
		return $this->__get($name);
	}
	
	public function offsetSet($name, $value)
	{
		$this->__set($name, $value);
	}
	
	

    public function __get($name)
    {
        if(!$this->has($name)) throw new Exception("service $name doesn't exist !");

        $serviceInstance = (is_callable($this->services[$name])) ? $this->services[$name]($this) : $this->services[$name];

        return $serviceInstance;
    }
	
	public function raw($name)
	{
		return $this->services[$name];
	}

    public function merge(array $data)
    {
        $this->services = array_merge($this->services, $data);
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

    public function protect(Closure $serviceHandle)
    {
        return function($c) use ($serviceHandle)
        {
          return $serviceHandle;
        };
    }

    public function get($name) { return $this->__get($name); }
}