<?php


class mySystemUtils
{
	const SERVER_VERSION = 'VERSION';
	const MYSQL = 'MYSQL';
	const SPHINX = 'SPHINX';
	const ELASTIC = 'ELASTIC';
	const CACHE = 'MEMCACHE_LOCAL';
	const LOCAL_FILE_CREATION = 'LOCAL_FILE_CREATION';
	const MOVE_FILE = 'MOVE_FILE';

	const DEFAULT_FILE_PATH = '/tmp/storage_test_file_';

	protected static $maxProcessTime = 5;
	

	public static function getHealthCheckInfo()
	{
		if(kConf::hasMap('health_check'))
		{
			$config = kConf::getMap("health_check");
		}


		if(!isset($config))
		{
			return '';
		}

		$hostname = infraRequestUtils::getHostname();
		$filePathPrefix = isset($config['filePathPrefix']) ? ($config['filePathPrefix']) : self::DEFAULT_FILE_PATH;
		$fileName = $filePathPrefix . $hostname;
		if(isset($config['maxProcessTime']))
		{
			self::$maxProcessTime = $config['maxProcessTime'];
		}

		$healthCheckArray = array();
		$healthCheckArray[self::SERVER_VERSION] = self::getVersion();
		$healthCheckArray[self::MYSQL] = (int)self::pingMySql();
		$healthCheckArray[self::SPHINX]  = self::pingSphinx($config);
		$healthCheckArray[self::ELASTIC] = self::pingElastic($config);
		$healthCheckArray[self::CACHE] = self::pingCache();
		$healthCheckArray[self::LOCAL_FILE_CREATION] = self::createLocalFile($fileName);
		$healthCheckArray[self::MOVE_FILE] = self::moveFile($fileName, $config);

		$strInfo = self::createHealthCheckStr($healthCheckArray);
		$notifyError = self::shouldNotifyError($healthCheckArray, $config);

		return array($strInfo, $notifyError);
	}


	public static function getVersion()
	{
		$version = file_get_contents(realpath(dirname(__FILE__)) . '/../../../../VERSION.txt');
		return trim($version);
	}

	public static function ping()
	{
		if(function_exists('apc_fetch') && apc_fetch(self::APIV3_FAIL_PING))
		{
			return false;
		}

		return true;
	}

	public static function pingMySql()
	{
		$hostname = infraRequestUtils::getHostname();
		$server = ApiServerPeer::retrieveByHostname($hostname);
		if(!$server)
		{
			$server = new ApiServer();
			$server->setHostname($hostname);
		}

		$server->setUpdatedAt(time());
		if(!$server->save())
		{
			return false;
		}

		return true;
	}

	public static function pingSphinx($config)
	{

		if(!isset($config['sphinxServer']) || !isset($config['sphinxPort']))
		{
			return 1;
		}
		$sphinxServer = $config['sphinxServer'];
		$port = $config['sphinxPort'];

		try
		{
			$dsn = "mysql:host=$sphinxServer;port=$port;";
			$con = new PDO($dsn);
			if(!$con)
			{
				return 0;
			}

			return self::runSphinxQuery($con, $config);
		}
		catch(Exception $e)
		{
			return 0;
		}
	}

	protected static function runSphinxQuery($con, $config)
	{
		$id = isset($config['sphinxEntryId']) ? $config['sphinxEntryId'] : 1;
		$sql = "select str_entry_id from kaltura_entry where id = " . $id;
		$stmt = $con->query($sql);
		if(!$stmt)
		{
			return 0;
		}

		$ids = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
		{
			if($ids)
			{
				return 1;
			}
		}
		return 0;
	}

	public static function pingElastic($config)
	{
		if(!isset($config['elasticEntryId']))
		{
			return 1;
		}

		try
		{
			$entryItem = new ESearchEntryItem();
			$entryItem->setFieldName(ESearchEntryFieldName::ID);
			$entryItem->setItemType(ESearchItemType::EXACT_MATCH);
			$entryItem->setSearchTerm($config['elasticEntryId']);

			$items = array($entryItem);
			$operator = new ESearchOperator();
			$operator->setOperator(ESearchOperatorType::AND_OP);
			$operator->setSearchItems($items);

			$entrySearch = new kEntrySearch();
			$results = $entrySearch->doSearch($operator);

			if(!$results[kESearchCoreAdapter::HITS_KEY][kESearchCoreAdapter::TOTAL_KEY])
			{
				return 0;
			}
		}
		catch(Exception $e)
		{
			return 0;
		}
		return 1;
	}


	public static function pingCache()
	{
		try
		{
			$memcache = kCacheManager::getSingleLayerCache(kCacheManager::CACHE_TYPE_API_V3);
			if (!$memcache)
			{
				return 0;
			}
		}
		catch(Exception $e)
		{
			return 0;
		}

		return 1;
	}

	public static function createLocalFile($fileName)
	{
		$cmd = 'head -c 1M </dev/urandom >' . $fileName . '| echo $! & ';
		return self::execCmd($cmd);
	}

	public static function moveFile($fileName, $config)
	{
		if(!isset($config['destFilePath']))
		{
			return 1;
		}
		$destPath = $config['destFilePath'];

		$cmd = 'mv -f ' . $fileName . ' ' . $destPath . ' 2>/dev/null ' . '| echo $! & ';
		return self::execCmd($cmd);
	}

	protected static function execCmd($cmd)
	{
		$pid = null;
		KalturaLog::debug('EXEC: ' . $cmd);

		try
		{
			$pid = shell_exec($cmd);
			$pid = str_replace(PHP_EOL, '', $pid);
			return self::checkProcessFinished($pid);
		}
		catch (Exception $e)
		{
			self::killProcess($pid);
			return 0;
		}
	}

	protected static function checkProcessFinished($pid)
	{
		for ($i = 0; $i < self::$maxProcessTime; $i++)
		{
			$process = shell_exec('kill -0 ' . $pid . ' 2> /dev/null');
			if(!$process)
			{
				return 1;
			}
			sleep(1);
		}
		self::killProcess($pid);
		return 0;
	}
	
	protected static function killProcess($pid)
	{
		if(isset($pid))
		{
			shell_exec('kill ' . $pid . ' 2> /dev/null');
		}
	}

	public static function createHealthCheckStr($healthCheckArray)
	{
		$result = '';
		foreach($healthCheckArray as $key => $value)
		{
			if($key == self::SERVER_VERSION)
			{
				$result .= 'server_health{Version="' . $value . '"} 1' . PHP_EOL;
			}
			else
			{
				$result .= 'server_health{check="' . $key . '"} ' . $value . PHP_EOL;
			}
		}
		return $result;
	}

	public static function shouldNotifyError($healthCheckArray, $config)
	{
		if(!isset($config['maxErrorNum']))
		{
			return false;
		}

		$cnt = 0;
		foreach ($healthCheckArray as $key => $value)
		{
			if ($value === 0)
			{
				$cnt++;
			}
		}

		if($cnt > $config['maxErrorNum'])
		{
			return true;
		}

		return false;
	}

}