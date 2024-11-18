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
		$url = parent::buildServiceUrl($hostName, null, false);
		return str_replace('/v1', '', $url);
	}

	public static function buildScriptUrl($hostName)
	{
		$url = parent::buildServiceUrl($hostName . '-ui', null, false);
		return str_replace('/v1', '', $url);
	}
}
