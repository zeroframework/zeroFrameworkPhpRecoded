#!/usr/bin/env php
<?php

use \Symfony\Component\Console\Helper\HelperSet;
use \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper;
use \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\Console\Input\ArgvInput;
// Cron manager

set_time_limit(0);

require __DIR__.'/../vendor/autoload.php';

$input = new ArgvInput();
$env = $input->getParameterOption(array("--env", "-e"), "prod");
$app = new app($env);

$libs_directory = $app->getCoreDirectory()."/lib";

$libs_files = glob($libs_directory."/*.php");

// Parcour un tableau et appele la fonction anonyme passé en deuxieme parametre sur chaque element du tableau
array_walk($libs_files, function($lib)
{
    // Ici on inclu dynamiquement toutes les libs .php du dossier lib :) (pratique)
    require_once($lib);
});

\baseBundle::register($app);

\consoleBundle::register($app);

$container = $app->getServiceContainer();

$app->run();

$container->get("console")->run($input);







