<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 26/04/13
 * Time: 21:57
 * To change this template use File | Settings | File Templates.
 */

use \Psr\Log\LoggerInterface;

use \Psr\Log\LoggerAwareTrait;

use \Psr\Log\LoggerAwareInterface;

use \Psr\Log\LogLevel;

class event implements LoggerAwareInterface {

        use LoggerAwareTrait;

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

        /**
         * @return bool
         */
        private function hasLogger()
        {
            return $this->logger !== null;
        }

        /**
         * @return LoggerInterface
         */
        private function getLogger()
        {
            return $this->logger;
        }

        private function hasEvent($name)
        {
            return isset($this->events[$name]);
        }

        public function listenEvent($name, $handle, $priority = self::PRIORITY_LOW)
        {
            if(!$this->hasEvent($name)) $this->addEvent($name);

            $this->events[$name][$priority][] = $handle;

            if($this->hasLogger()) $this->getLogger()->log(LogLevel::DEBUG, "[EVENT] Element start listen event $name with priority $priority");
        }

        public function notify($name)
        {
	        if(!$this->hasEvent($name)) $this->addEvent($name);

             ksort($this->events[$name]);

            if($this->hasLogger()) $this->getLogger()->log(LogLevel::DEBUG, "[EVENT] $name trigger started");

             foreach($this->events[$name] as $listeners)
             {
                 foreach($listeners as $listener)
                 {
                     call_user_func_array($listener, func_get_args());

                     if($this->hasLogger()) $this->getLogger()->log(LogLevel::DEBUG, "[EVENT] $name pass to listener ''");
                 }
             }

            if($name != "onEvent") $this->notify("onEvent", $name);
        }
}