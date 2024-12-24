<?php

require_once (dirname(__FILE__).'/../bootstrap.php');

class systemUtils
{
	const MEMCACHE = 'MEMCACHE';
	const ELASTIC = 'ELASTIC';
	const ELASTIC_HOST = '127.0.0.1';
	const ELASTIC_PORT = '9200';
	const ELASTIC_HEALTH_CHECK = '/_cluster/health?pretty';

	protected static $checkFunc = array(
		self::MEMCACHE => 'systemUtils::pingMemcache',
		self::ELASTIC => 'systemUtils::pingElastic',
	);

	public static function getHealthCheckInfo($checkedComponents)
	{
		$healthCheckArray = array();
		foreach ($checkedComponents as $component)
		{
			if (isset(self::$checkFunc[$component]))
			{
				$healthCheckArray[$component] = call_user_func(self::$checkFunc[$component]);
			}
		}

		$result = '';
		foreach ($healthCheckArray as $component => $res)
		{
			if (!$res)
			{
				$result .= "health check failed for $component " . PHP_EOL;
			}
		}

		return $result;
	}

	public static function pingElastic()
	{
		$elasticHost = kConf::get('elasticHost', 'elastic', null);
		if (!$elasticHost)
		{
			$elasticHost = self::ELASTIC_HOST;
		}
		$elasticPort = kConf::get('elasticPort', 'elastic', null);
		if (!$elasticPort)
		{
			$elasticPort = self::ELASTIC_PORT;
		}
		try
		{
			$url = 'http://' . $elasticHost . ':' . $elasticPort . self::ELASTIC_HEALTH_CHECK;
			$elasticHealth = json_decode(KCurlWrapper::getContent($url), true);
			if (!isset($elasticHealth['status']) || $elasticHealth['status'] == 'red')
			{
				return 0;
			}
		}
		catch (Exception $e)
		{
			return 0;
		}
		return 1;
	}

	public static function pingMemcache()
	{
		$memcache = getenv('MEMCACHE_1');
		list($memcacheHost, $memcachePort) = explode(':', $memcache);

		$memc = new kInfraMemcacheCacheWrapper();
		return $memc->init(array('host'=>$memcacheHost, 'port'=>$memcachePort));
	}

}

if ($argc < 2)
{
	echo "Usage:\n\t" . basename(__file__) . " <components to check>\n";
	exit(1);
}

$components = explode(",", $argv[1]);
$errMessage = systemUtils::getHealthCheckInfo($components);
if ($errMessage)
{
	echo $errMessage;
	exit(1);
}
exit(0);
