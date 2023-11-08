<?php
/**
 * Auth Manager Micro Service
 * This represents the 'auth-manager' service under 'plat-auth' repo
 */
class MicroServiceAuthManager extends MicroServiceAuthBase
{
	public static $serviceName = 'auth-manager';
	
	public function __construct()
	{
		parent::__construct(MicroServiceAuthManager::$serviceName);
	}
}
