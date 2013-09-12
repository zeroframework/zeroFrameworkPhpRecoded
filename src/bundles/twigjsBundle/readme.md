<?php
require __DIR__.'/../vendor/autoload.php';
include __DIR__."/../zfboot.php";

use TwigJs\CompileRequest;

$folder_html = __DIR__."/html";
$folter_js_twig = __DIR__."/../js/twig";

if(!isset($argv[1])) throw new \Exception("Pas assez d'argument");

$file = $folder_html."/".$argv[1].".html.twig";

$container = $app->getServiceContainer();

$env = $container->get("twig");

$templatename = isset($argv[2]) ? $argv[2] : basename($file);

$handler = new TwigJs\CompileRequestHandler($env, new TwigJs\JsCompiler($env));

$compileRequest = new CompileRequest($templatename, file_get_contents($file));

file_put_contents($folter_js_twig."/".$templatename.".js", $handler->process($compileRequest));

echo "Fichier ".$argv[1]." compiler !";