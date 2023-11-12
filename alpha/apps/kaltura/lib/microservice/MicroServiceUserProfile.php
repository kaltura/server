<?php
/**
 * User Profile Micro Service
 * This represents the 'user-profile' service under 'plat-user' repo
 */
class MicroServiceUserProfile extends MicroServiceBaseService
{
	public function __construct()
	{
		parent::__construct(MicroServiceHost::USER, MicroServiceService::USER_PROFILE);
	}
}
