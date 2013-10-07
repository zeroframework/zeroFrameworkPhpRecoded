<?php
class core {
      private $name = "default";

      protected function setName($name)
      {
          $this->name = $name;
      }

      public function getName()
      {
          return $this->name;
      }

      public function getCoreDirectory()
      {
            return self::_getCoreDirectory();
      }

      public static function _getCoreDirectory()
      {
          return __DIR__;
      }
}

?>