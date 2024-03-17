<?php
/**
 * Media Repurposing Micro Service
 */
class MicroServiceMediaRepurposing extends MicroServiceBaseService
{
	public static $host = 'mr';
	public static $service = '';

	public function __construct()
	{
		$this->hostName = self::$host;
		$this->serviceName = self::$service;
		parent::__construct();
	}
}
