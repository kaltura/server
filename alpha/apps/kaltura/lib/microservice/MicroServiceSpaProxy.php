<?php
/**
 * Spa Proxy Micro Service
 * This represents the 'spa-proxy' service under 'plat-auth' repo
 */
class MicroServiceSpaProxy extends MicroServiceAuthBase
{
	public static $serviceName = 'spa-proxy';
	
	public function __construct()
	{
		parent::__construct(MicroServiceSpaProxy::$serviceName);
	}
}
