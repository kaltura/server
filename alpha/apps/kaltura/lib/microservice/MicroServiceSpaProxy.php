<?php
/**
 * Spa Proxy Micro Service
 * This represents the 'spa-proxy' service under 'plat-auth' repo
 */
class MicroServiceSpaProxy extends MicroServiceAuthBase
{
	public static $service = 'spa-proxy';
	
	public function __construct()
	{
		$this->serviceName = self::$service;
		parent::__construct();
	}
}
