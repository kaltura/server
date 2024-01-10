<?php
/**
 * Base Auth Micro Services Class
 * This is the base class for all services under 'plat-auth' repo
 */
abstract class MicroServiceAuthBase extends MicroServiceBaseService
{
	public static $host = 'auth';
	
	public function __construct()
	{
		$this->hostName = self::$host;
		parent::__construct();
	}
}
