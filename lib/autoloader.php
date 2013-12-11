<?php
class autoloader {

     private static $instance = null;
     private $basesDirectory = array();

     private $cache = array();

     private $cacheUpdated = false;

     private $strictMode = false;

     public function __construct()
     {

     }

    /**
     * @return autoloader|null
     * Ici on retrouve le design pattern du 'singleton'
     * En appelant cette classe uniquement via autoloader::getInstance()
     *
     * On s'assure que la classe ne soit instancié qu'une seule et unique fois
     * Si elle est déjà instancié on retoaurne son instance mis en propriété static
     * Sinon on l'instancie puis on retour l'instance en question
     * P.S: self fait référence a la classe ou on l'utilise
     */
    public static function getInstance()
    {
         if(self::$instance == null) self::$instance = new self();

        return self::$instance;
    }

    private function getBasesDirectory()
    {
        return $this->basesDirectory;
    }

    public function strict()
    {
        $this->strictMode = true;
    }

    public function isStrict()
    {
        return $this->strictMode;
    }

    public function addBaseDirectory($baseDirectory)
    {
        $this->basesDirectory[] = $baseDirectory;
    }

     public static function register()
     {
           $instance = self::getInstance();

           // Spl autoload register
           /**
            * Cela fait partie de la spl (standart program library) de php
            * Ici on lui place un callback
            * Qui peut etre soit le nom d'une fonction a appeller
            * Soit dans la cas d'une methode
            * Un tableau en parmetre dont le premier index est l'instance de l'objet qui contient la méthode
            * Ou le nom de la classe si la methode est statique
            * Et en deuxieme parametre le nom de cette méthode
            *
            * Le callback final attendra en parametre le nom de la classe a charger
            * Et devra fait le néscésaire pour charger la classe en question
            */
           spl_autoload_register(array($instance, "load"));

           return $instance;
     }

    public function fillCache($file)
    {
        if(!$this->cacheUpdated) return;

        file_put_contents($file, json_encode($this->cache));
    }

    public function loadCache($file)
    {
        $this->cache = json_decode(file_get_contents($file), true);
    }

     public function load($className)
     {
         if(isset($this->cache[$className])) {
             include($this->cache[$className]);
             return;
         }

         foreach($this->basesDirectory as $baseDirectory)
         {
            // foreach(new DirectoryIterator($baseDirectory) as $file)
             //{
                $path = $baseDirectory."/".$className.".php";
				$path = str_replace(array("/","\\"), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $path);
                 $file = new SplFileInfo($path);
                 if($file->isFile())
                 {
                     $this->cache[$className] = $file->getRealPath();
                     $this->cacheUpdated = true;
                     include_once($file->getRealPath());

                     return;
                 }
             //}
         }

        if($this->isStrict())  new \Exception("Autoloader doesn't load class $className");
     }
}
?>
