<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 25/04/13
 * Time: 22:23
 * To change this template use File | Settings | File Templates.
 */

namespace service;

class test {
    private $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;

        echo "\r\nservice test instancier \r\n";
    }

    public function test()
    {
        $this->logger->info("message du service log");

        return "ceci est un service de test";
    }
}