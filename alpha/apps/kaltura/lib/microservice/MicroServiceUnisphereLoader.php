<?php
/**
 * Unisphere Micro Service
 */
class MicroServiceUnisphereLoader extends MicroServiceBaseService
{
	public static $host = 'unisphere';
	public static $service = '';

	public function __construct()
	{
		$this->hostName = self::$host;
		parent::__construct();
	}
}
