<?php

namespace Model;

use interfaces\containerAwaireInterface;
use Symfony\Component\Console\Command\Command;

class ContainerCommand extends Command implements containerAwaireInterface
{
    public function __construct($container)
    {
        $this->setContainer($container);

        parent::__construct();
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }


    /**
     * @return \service\ApplicationManager
     */
    public function getApplicationManager()
    {
        return $this->getContainer()->get("app.manager");
    }
}