<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 25/04/13
 * Time: 22:23
 * To change this template use File | Settings | File Templates.
 */

namespace service;

use \Psr\Log\AbstractLogger;

class logger extends AbstractLogger {

	private $file;

    private $loglevels;

    public function __construct(array $loglevels)
    {
        $this->loglevels = $loglevels;

        if(empty($loglevels)) return;

	    try {
            $this->file = new \SplFileObject(APP_DIRECTORY."/log.txt", "a+");
        }
        catch(\Exception $e)
        {

            if(PHP_SAPI == "CLI")
            {
                echo "Un probleme de droit sur ".APP_DIRECTORY."/log.txt est survenu \r\n";
            }

            $this->file = null;
        }

	    //$this->file->flock(\LOCK_EX);

	    if($this->isFile()) {
            $this->file->fwrite("=================== Start request ".date("H:i:s")." ================\r\n");
        }

    }

    public function isFile()
    {
        return !empty($this->file);
    }

	public function __destruct()
	{
        if($this->isFile()) $this->file->fwrite("=================== End Request ===================\r\n");

		//$this->file->flock(\LOCK_UN);
	}

    public function log($logLevel, $message, array $context = array())
    {
        if(!in_array($logLevel, $this->loglevels)) return;

        if($this->isFile()) $this->file->fwrite("[".date("d/m/Y H:i:s")."] [LOG $logLevel] ".$message."\r\n");
    }

    public function onReady()
    {
        $this->info("framework started");
    }
}