<?php

namespace service;

use interfaces\containerAwaireInterface;

class DecodaTwigHelper extends \Twig_Extension implements containerAwaireInterface
{
    private $container;


    public function __construct($container)
    {
        $this->setContainer($container);
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "decoda.twig.helper";
    }

    public function getFilters()
    {
        return array(
            "bbcode" => new \Twig_Filter_Method($this, "parse", array('is_safe' => array('html')))
        );
    }

    public function parse($string)
    {
        $parser = $this->getContainer()->get("decoda");

        $parser->reset($string);

        return $parser->parse();
    }


    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }
}