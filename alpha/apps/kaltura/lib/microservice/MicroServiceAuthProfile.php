<?php
/**
 * Auth Profile Micro Service
 * This represents the 'auth-profile' service under 'plat-auth' repo
 */
class MicroServiceAuthProfile extends MicroServiceBaseService
{
	public function __construct()
	{
		parent::__construct(MicroServiceHost::AUTH, MicroServiceService::AUTH_PROFILE);
	}
}
