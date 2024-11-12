<?php
/**
 * Checklist for Micro Service
 */

class MicroServiceChecklist extends MicroServiceBaseService
{
	public static $host = 'checklist';
	public function __construct()
	{
		$this->hostName = self::$host;
		parent::__construct();
	}

	public static function buildServiceUrl($hostName, $serviceName , $isApi = true)
	{
		$url = parent::buildServiceUrl($hostName, false, false);
		return str_replace('/v1', '', $url);
	}

	public static function buildScriptUrl($hostName)
	{
		$url = parent::buildServiceUrl($hostName . '-ui', false, false);
		return str_replace('/v1', '', $url);
	}
}