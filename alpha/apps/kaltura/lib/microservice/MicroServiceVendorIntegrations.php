<?php
/**
 * Vendor Integrations Micro Service
 */
class MicroServiceVendorIntegrations extends MicroServiceBaseService
{
	public static $host = 'vendor-integrations';
	public static $service = '';

	public function __construct()
	{
		$this->hostName = self::$host;
		parent::__construct();
	}
}
