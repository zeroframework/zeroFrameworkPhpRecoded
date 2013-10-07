<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 26/04/13
 * Time: 21:57
 * To change this template use File | Settings | File Templates.
 */

class event {
        const PRIORITY_LOW = 3;
        const PRIORITY_MEDIUM = 2;
        const PRIORITY_HIGH = 1;



         private $events = array();

        public function __construct()
        {
            $this->addEvent("onEvent");
        }

        private function addEvent($name)
        {
            if(isset($this->events[$name])) return;

            $this->events[$name] = array(
                self::PRIORITY_LOW => array(),
                self::PRIORITY_MEDIUM => array(),
                self::PRIORITY_HIGH => array(),
            );
        }

        private function hasEvent($name)
        {
            return isset($this->events[$name]);
        }

        public function listenEvent($name, $handle, $priority = self::PRIORITY_LOW)
        {
            if(!$this->hasEvent($name)) $this->addEvent($name);

            $this->events[$name][$priority][] = $handle;
        }

        public function notify($name)
        {
	        if(!$this->hasEvent($name)) $this->addEvent($name);

             ksort($this->events[$name]);

             foreach($this->events[$name] as $listeners)
             {
                 foreach($listeners as $listener)
                 {
                     call_user_func_array($listener, func_get_args());
                 }
             }

            if($name != "onEvent") $this->notify("onEvent", $name);
        }
}