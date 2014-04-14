<?php

class conf {

    public function __construct()
    {

    }

    public function isCache()
    {
        static $apc;

        if($apc === null) $apc = (PHP_SAPI != "cli" && extension_loaded("apc"));

        return $apc;
    }

    public function loadConfigurationFile($name, $directory)
    {
        $pathWithoutExtension = $directory.DIRECTORY_SEPARATOR.$name;

        $key = md5("framework_configuration_".$directory."_".$name);

        if(!$this->isCache() || !apc_exists($key))
        {
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

            if(!isset($vars)) {
                throw new \Exception("configuration doesn't load $directory : $name !");
            }

            if($this->isCache()) 
            {
                apc_store($key, $vars);
            }
        }

        if($this->isCache() && apc_exists($key))
        {
            $vars = apc_fetch($key);
        }

        if(array_key_exists("imports", $vars))
        {
            $dataImporteds = array();

            foreach($vars["imports"] as $file)
            {
                $dataImporteds = array_merge($dataImporteds, $this->loadConfigurationFile($file, $directory));
            }

            $vars = array_merge($dataImporteds, $vars);
        }

        return $vars;
    }
}