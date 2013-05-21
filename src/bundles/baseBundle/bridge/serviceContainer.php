<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 08/05/13
 * Time: 23:12
 * To change this template use File | Settings | File Templates.
 */

namespace bridge;

trait serviceContainer
{
    private $serviceContainer;

    public function getServiceContainer()
    {
        if(empty($this->serviceContainer)) $this->serviceContainer = new \serviceContainer();

        return $this->serviceContainer;
    }
}