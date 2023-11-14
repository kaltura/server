<?php
/**
 * App Subscription Micro Service
 * This represents the 'app-subscription' service under 'plat-auth' repo
 */
class MicroServiceAppSubscription extends MicroServiceBaseService
{
	public function __construct()
	{
		parent::__construct(MicroServiceHost::AUTH, MicroServiceService::APP_SUBSCRIPTION);
	}
}
