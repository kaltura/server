<?php
/**
 * App Subscription Micro Service
 * This represents the 'app-subscription' service under 'plat-auth' repo
 */
class MicroServiceAppSubscription extends MicroServiceAuthBase
{
	public static $service = 'app-subscription';
	
	public function __construct()
	{
		$this->serviceName = self::$service;
		parent::__construct();
	}
}
