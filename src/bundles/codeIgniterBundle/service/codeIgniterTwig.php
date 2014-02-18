<?php

namespace service;

class codeIgniterTwig extends \Twig_Extension
{
    private $codeIgniterLibrary;
    private $request;
    private $container;

    public function __construct($codeIgniterLibrary, $request, $container)
    {
        $this->codeIgniterLibrary = $codeIgniterLibrary;
        $this->request = $request;
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'anchor' => new \Twig_Function_Method($this, 'anchor', array('is_safe' => array('html'))),
            'trans' => new \Twig_Function_Method($this, 'trans', array('is_safe' => array('html'))),
	        'translate' => new \Twig_Function_Method($this, 'translate', array('is_safe' => array('html'))),
            'css_url' => new \Twig_Function_Method($this, 'css_url', array('is_safe' => array('html'))),
            'base_url' => new \Twig_Function_Method($this, 'base_url', array('is_safe' => array('html'))),
            'image_url' => new \Twig_Function_Method($this, 'image_url'),
            'site_url' => new \Twig_Function_Method($this, 'site_url', array('is_safe' => array('html'))),
            'load' => new \Twig_Function_Method($this, 'load', array('is_safe' => array('html'))),
            'form_error' => new \Twig_Function_Method($this, 'form_error', array('is_safe' => array('html'))),
            'get_user_timezone' => new \Twig_Function_Function("get_user_timezone"),
            'timezone_menu' => new \Twig_Function_Function("timezone_menu", array('is_safe' => array('html'))),
            'local_to_gmt' => new \Twig_Function_Function("local_to_gmt"),
            'get_user_time' => new \Twig_Function_Function("get_user_time"),
            'getListImage' => new \Twig_Function_Function("getListImage"),
            'date' => new \Twig_Function_Function("date"),
            'get_user_by_id' => new \Twig_Function_Function("get_user_by_id"),
            'get_currency_code' => new \Twig_Function_Function("get_currency_code"),
            'is_logged' => new \Twig_Function_Method($this, "is_logged"),
            'user' => new \Twig_Function_Method($this, "getUser"),
            "assets_url" => new \Twig_Function_Method($this, "assets_url"),
            "forward" => new \Twig_Function_Method($this, "forward"),
            "get_user_times" => new \Twig_Function_Function("get_user_times"),
            "get_list_by_id" => new \Twig_Function_Function("get_list_by_id"),
            "get_currency_symbol" => new \Twig_Function_Function("get_currency_symbol"),
            "get_currency_value" => new \Twig_Function_Function("get_currency_value"),
        );
    }

    public function getDomaine()
    {
        static $domaine;

        if(null !== $domaine) return $domaine;

        $domaine = $this->container->get("domaine");

        return $domaine;
    }

    public function assets_url($file)
    {
        $scheme = ($this->container->has("request")) ? $this->container->get("request")->getScheme() : "http";

        return $scheme."://".$this->getDomaine()."/assets/".$file;
    }

    public function is_logged()
    {
        //&& (!$this->facebook_lib->logged_in())

        return ($this->load("dx_auth")->is_logged_in());
    }

    public function load($name)
    {
        return $this->codeIgniterLibrary->get($name);
    }

    public function form_error($name)
    {

    }

    public function forward($controlller, $method, $parameters = array())
    {
        $controllerInstance = new $controlller();

        call_user_func_array(array($controllerInstance, $method), $parameters);
    }

    public function css_url()
    {
        $scheme = ($this->container->has("request")) ? $this->container->get("request")->getScheme() : "http";

        return $scheme."://".$this->getDomaine()."/css/templates/blue";
    }

    public function base_url()
    {
        $scheme = ($this->container->has("request")) ? $this->container->get("request")->getScheme() : "http";

        return $scheme."://".$this->getDomaine()."/";
    }

    public function image_url()
    {
        $scheme = ($this->container->has("request")) ? $this->container->get("request")->getScheme() : "http";

        return $scheme."://".$this->getDomaine()."/";
    }

    public function site_url($path = "")
    {
        $scheme = ($this->container->has("request")) ? $this->container->get("request")->getScheme() : "http";

        return $scheme."://".$this->getDomaine()."/".(($this->container->get("debug")) ? "index_dev.php/" : "").$path;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('trans', array($this, 'trans')),
        );
    }

    public function getGlobals()
    {
        return array(
           "THEME_FOLDER" => "d",
	        "uri" => array("segment" => $this->getSegments()),
            "baseurl" => "http://test.adibox.com/",
        );
    }

    public function getSegments()
    {
       return array(
          1 => "lol",
	  2 => "paslol",
       );
    }

    public function anchor($route, $content, $options = array())
    {
        $defaultsOption = array(
            "class" => "",
        );

        $options = array_merge($defaultsOption, $options);

	    return "<a href='".$this->base_url().$route."' class='".implode(" ", $options)."'>$content</a>";
    }

    public function trans($name, $data = array(), $domaine = "messages")
    {
        if(empty($name)) return $name;

        $translated = (function_exists("translate")) ? translate($name) : $name;

        if($translated != $name) return $translated;

        if($this->container->has("translator"))
        {
            $translated = $this->container->get("translator")->trans($name, $data, $domaine);

            if($translated != $name) return $translated;
        }

        return $name;
    }

    public function translate($name)
    {
        return $this->trans($name);
    }

    public function priceFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
    {
        $price = number_format($number, $decimals, $decPoint, $thousandsSep);
        $price = '$'.$price;

        return $price;
    }

	public function getName()
	{
		return "twig_extension_codeIgniter";
	}
}

?>
