<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 08/05/13
 * Time: 22:57
 * To change this template use File | Settings | File Templates.
 */

namespace bridge;

use \conf as confLib;

trait conf {
      private $conf;

      public function getConf()
      {
          if(empty($this->conf)) $this->conf = new confLib();

          return $this->conf;
      }


}