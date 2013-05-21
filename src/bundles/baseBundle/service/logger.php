<?php
/**
 * Created by IntelliJ IDEA.
 * User: Gauthier
 * Date: 25/04/13
 * Time: 22:23
 * To change this template use File | Settings | File Templates.
 */

namespace service;

class logger {

	private $file;

    public function __construct()
    {

	    $this->file = new \SplFileObject(APP_DIRECTORY."/log.txt", "w+");

	    //$this->file->flock(\LOCK_EX);

	    $this->file->fwrite("=================== Start request ".date("H:i:s")." ================\r\n");

    }

	public function __destruct()
	{
		$this->file->fwrite("=================== End Request ===================\r\n");

		//$this->file->flock(\LOCK_UN);
	}

    public function info($log)
    {
        $this->file->fwrite("[LOG] ".$log."\r\n");
    }

    public function onReady()
    {
        $this->info("c moi jesus christ");
    }
}