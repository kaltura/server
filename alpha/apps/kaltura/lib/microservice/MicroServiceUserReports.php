<?php
/**
 * User Profile Micro Service
 * This represents the 'reports' service under 'plat-user' repo
 */
class MicroServiceUserReports extends MicroServiceUserBase
{
	public static $service = 'reports';
	
	public function __construct()
	{
		$this->serviceName = self::$service;
		parent::__construct();
	}
}
