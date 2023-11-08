<?php
/**
 * Auth Profile Micro Service
 * This represents the 'auth-profile' service under 'plat-auth' repo
 */
class MicroServiceAuthProfile extends MicroServiceAuthBase
{
	public static $serviceName = 'auth-profile';
	
	public function __construct()
	{
		parent::__construct(MicroServiceAuthProfile::$serviceName);
	}
}
