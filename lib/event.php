<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 26/04/13
 * Time: 21:57
 * To change this template use File | Settings | File Templates.
 */

class event {
         private $events = array();

        private function addEvent($name)
        {
            $this->events[$name] = array();
        }

        private function hasEvent($name)
        {
            return isset($this->events[$name]);
        }

        public function listenEvent($name, $handle)
        {
            if(!$this->hasEvent($name)) $this->addEvent($name);

            $this->events[$name][] = $handle;
        }

        public function notify($name)
        {
	        if(!$this->hasEvent($name)) $this->addEvent($name);

             foreach($this->events[$name] as $listener)
             {
                    call_user_func_array($listener, func_get_args());
             }
        }
}