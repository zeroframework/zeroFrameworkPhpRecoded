<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adibox
 * Date: 26/09/13
 * Time: 16:46
 * To change this template use File | Settings | File Templates.
 */

namespace service;


class gregwarImage extends \Twig_Extension {
    private $options = array();

    private $container;

    public function __construct(array $options, \serviceContainer $container)
    {
        $this->options = $options;

        $this->container = $container;
    }

    public function getName()
    {
        return "gregwarimage.twig.extension";
    }

    public function getFunctions()
    {
        return array(
            'image' => new \Twig_Function_Method($this, 'image', array('is_safe' => array('html'))),
            'new_image' => new \Twig_Function_Method($this, 'newImage', array('is_safe' => array('html')))
        );
    }

    public function image($path)
    {
        return $this->open($path);
    }

    public function newImage($width, $height)
    {
        return $this->create($width, $height);
    }

    public function open($file)
    {
        return $this->createInstance($file);
    }

    public function create($w, $h)
    {
        return $this->createInstance(null, $w, $h);
    }

    private function createInstance($file, $w = null, $h = null)
    {

        $handler_class = $this->options["handler_class"];

        $web_dir = $this->options["web_dir"];

        $cache_dir = realpath($this->options["cache_dir"]);

        $image = new $handler_class($file, $w, $h, null);

        $image->setCacheDir($this->options["cache_dir"]);

        $image->setFileCallback(function($file) use ($web_dir, $cache_dir)
        {
           return str_replace($cache_dir, $web_dir, realpath($file));
        });

        return $image;
    }
}