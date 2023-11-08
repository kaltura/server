<?php
/**
 * App Subscription Micro Service
 * This represents the 'app-subscription' service under 'plat-auth' repo
 */
class MicroServiceAppSubscription extends MicroServiceAuthBase
{
	public static $serviceName = 'app-subscription';
	
	public function __construct()
	{
		parent::__construct(MicroServiceAppSubscription::$serviceName);
	}
}
