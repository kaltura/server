<?php
/**
 * User Profile Micro Service
 * This represents the 'user-profile' service under 'plat-user' repo
 */
class MicroServiceUserProfile extends MicroServiceUserBase
{
	public static $serviceName = 'user-profile';
	
	public function __construct()
	{
		parent::__construct(MicroServiceUserProfile::$serviceName);
	}
}
