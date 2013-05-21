<?php
class core {
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