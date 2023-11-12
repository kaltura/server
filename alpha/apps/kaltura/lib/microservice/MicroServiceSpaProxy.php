<?php
/**
 * Spa Proxy Micro Service
 * This represents the 'spa-proxy' service under 'plat-auth' repo
 */
class MicroServiceSpaProxy extends MicroServiceBaseService
{
	public function __construct()
	{
		parent::__construct(MicroServiceHost::AUTH, MicroServiceService::SPA_PROXY);
	}
}
