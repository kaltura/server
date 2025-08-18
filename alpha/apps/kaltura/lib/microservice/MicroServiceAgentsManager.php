<?php
/**
 * Agents Manager Micro Service
 */
class MicroServiceAgentsManager extends MicroServiceBaseService
{
	public static $host = 'agents-manager';
	public static $service = '';

	public function __construct()
	{
		$this->hostName = self::$host;
		$this->serviceName = self::$service;
		parent::__construct();
	}
}
