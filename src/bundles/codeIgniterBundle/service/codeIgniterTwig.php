<?php

namespace service;

class codeIgniterTwig extends \Twig_Extension
{
    private $codeIgniterLibrary;
    private $request;

    public function __construct($codeIgniterLibrary, $request)
    {
        $this->codeIgniterLibrary = $codeIgniterLibrary;
        $this->request = $request;
    }

    public function getFunctions()
    {
        return array(
            'anchor' => new \Twig_Function_Method($this, 'anchor', array('is_safe' => array('html'))),
            'trans' => new \Twig_Function_Method($this, 'trans', array('is_safe' => array('html'))),
	    'translate' => new \Twig_Function_Method($this, 'translate', array('is_safe' => array('html'))),
            'css_url' => new \Twig_Function_Method($this, 'css_url', array('is_safe' => array('html'))),
            'base_url' => new \Twig_Function_Method($this, 'base_url', array('is_safe' => array('html'))),
            'site_url' => new \Twig_Function_Method($this, 'site_url', array('is_safe' => array('html'))),
            'load' => new \Twig_Function_Method($this, 'load', array('is_safe' => array('html'))),
            'form_error' => new \Twig_Function_Method($this, 'form_error', array('is_safe' => array('html'))),
            'get_user_timezone' => new \Twig_Function_Function("get_user_timezone"),
            'timezone_menu' => new \Twig_Function_Function("timezone_menu", array('is_safe' => array('html'))),
            'local_to_gmt' => new \Twig_Function_Function("local_to_gmt"),
            'get_user_time' => new \Twig_Function_Function("get_user_time"),
            'date' => new \Twig_Function_Function("date"),
            'getListImage' => new \Twig_Function_Function("getListImage"),
            'get_user_by_id' => new \Twig_Function_Function("get_user_by_id"),
            'get_currency_code' => new \Twig_Function_Function("get_currency_code"),
            'is_logged' => new \Twig_Function_Method($this, "is_logged"),
            'user' => new \Twig_Function_Method($this, "user"),
        );
    }

    public function user()
    {
        return $this->load("dx_auth")->get_user();
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

    public function css_url()
    {
       return "http://test.adibox.com/css/templates/blue";
    }

    public function base_url()
    {
       return "http://test.adibox.com/";
    }

    public function site_url($path = "")
    {
       return "http://test.adibox.com/".$path;
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
            "app" => array(
                "request" => $this->request,
            ),
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

    public function trans($name)
    {
        $translated = translate($name);

        if($translated != $name) return $translated;

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
