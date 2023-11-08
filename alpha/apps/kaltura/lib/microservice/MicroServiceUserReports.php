<?php
/**
 * User Profile Micro Service
 * This represents the 'reports' service under 'plat-user' repo
 */
class MicroServiceReports extends MicroServiceUserBase
{
	public static $serviceName = 'reports';
	
	public function __construct()
	{
		parent::__construct(MicroServiceReports::$serviceName);
	}
}
