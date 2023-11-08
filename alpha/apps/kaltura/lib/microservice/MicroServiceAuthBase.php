<?php
/**
 * Base Auth Micro Services Class
 * This is the base class for all services under 'plat-auth' repo
 */
abstract class MicroServiceAuthBase extends MicroServiceBaseService
{
	public static $hostPrefix = 'auth';
	
	public function __construct($serviceName)
	{
		parent::__construct(MicroServiceAuthBase::$hostPrefix, $serviceName);
	}
}
