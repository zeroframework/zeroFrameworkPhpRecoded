<?php

namespace service;

use Exception\HumanException;

class codeIgniterLibrary
{
    private $library;

    public function onLoadLibraryCodeIgniter($eventName, $library)
    {
        $this->library = $library;
    }

    public function get($name)
    {
        return $this->library[$name];
    }
}