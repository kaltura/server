<?php
require_once __DIR__."/baseMemcacheConf.php";
class localCache extends baseMemcacheConf
{
	function __construct()
	{
		if(include(kEnvironment::getConfigDir().'/configCacheParams.php'))
		{
			if(isset($localCacheSourceConfiguration))
			{
				$port = $localCacheSourceConfiguration['port'];
				$host = $localCacheSourceConfiguration['host'];
				return  parent::__construct($port, $host);
			}
		}
	}
}
