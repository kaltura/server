<?php
/**
 * User Profile Micro Service
 * This represents the 'user-profile' service under 'plat-user' repo
 */
class MicroServiceUserProfile extends MicroServiceUserBase
{
	public static $service = 'user-profile';
	
	public function __construct()
	{
		$this->serviceName = self::$service;
		parent::__construct();
	}
}
