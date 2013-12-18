<?php

class conf {

    public function __construct()
    {

    }

    public function loadConfigurationFile($name, $directory)
    {
        $pathWithoutExtension = $directory.DIRECTORY_SEPARATOR.$name;

        if(file_exists($pathWithoutExtension.".ini"))
        {
            $vars = parse_ini_file($pathWithoutExtension.".ini", true);
        }
        elseif(file_exists($pathWithoutExtension.".json"))
        {
            $vars = json_decode(file_get_contents($pathWithoutExtension.".json"), true);
        }
        elseif(file_exists($pathWithoutExtension.".php"))
        {
            $vars = require_once($pathWithoutExtension.".php");
        }

        if(!isset($vars)) throw new \Exception("configuration doesn't load $directory : $name !");

        return $vars;
    }
}