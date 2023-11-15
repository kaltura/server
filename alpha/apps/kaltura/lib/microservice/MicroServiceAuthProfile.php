<?php
/**
 * Auth Profile Micro Service
 * This represents the 'auth-profile' service under 'plat-auth' repo
 */
class MicroServiceAuthProfile extends MicroServiceAuthBase
{
	public static $service = 'auth-profile';
	
	public function __construct()
	{
		$this->serviceName = self::$service;
		parent::__construct();
	}
}
