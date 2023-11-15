<?php
/**
 * Auth Manager Micro Service
 * This represents the 'auth-manager' service under 'plat-auth' repo
 */
class MicroServiceAuthManager extends MicroServiceAuthBase
{
	public static $service = 'auth-manager';
	
	public function __construct()
	{
		$this->serviceName = self::$service;
		parent::__construct();
	}
}
