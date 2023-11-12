<?php
/**
 * Auth Manager Micro Service
 * This represents the 'auth-manager' service under 'plat-auth' repo
 */
class MicroServiceAuthManager extends MicroServiceBaseService
{
	public function __construct()
	{
		parent::__construct(MicroServiceHost::AUTH, MicroServiceService::AUTH_MANAGER);
	}
}
