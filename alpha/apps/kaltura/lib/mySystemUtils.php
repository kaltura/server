<?php


class mySystemUtils
{
	const SERVER_VERSION = 'VERSION';
	const MYSQL = 'MYSQL';
	const SPHINX = 'SPHINX';
	const ELASTIC = 'ELASTIC';
	const FILE_CREATION = 'FILE_CREATION';
	const ELASTIC_HOST = '127.0.0.1';
	const ELASTIC_PORT = '9200';
	const ELASTIC_HEALTH_CHECK = '/_cluster/health?pretty';
	const SPHINX_QUERY = 'show tables';

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
		$healthCheckArray[self::SPHINX]  = self::pingSphinx();
		$healthCheckArray[self::ELASTIC] = self::pingElastic();
		$healthCheckArray[self::FILE_CREATION] = self::createFile($fileName);

		$strInfo = self::createHealthCheckStr($healthCheckArray);
		$notifyError = self::shouldNotifyError($healthCheckArray);

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

	public static function pingSphinx()
	{
		try
		{
			$con = DbManager::getSphinxConnection(true);
			if(!$con)
			{
				return 0;
			}

			return self::runSphinxQuery($con);
		}
		catch(Exception $e)
		{
			return 0;
		}
	}

	protected static function runSphinxQuery($con)
	{
		$stmt = $con->query(self::SPHINX_QUERY);
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

	public static function pingElastic()
	{
		$elasticHost = kConf::get('elasticHost', 'elastic', null);
		if(!$elasticHost)
		{
			$elasticHost = self::ELASTIC_HOST;
		}
		$elasticPort = kConf::get('elasticPort', 'elastic', null);
		if(!$elasticPort)
		{
			$elasticPort = self::ELASTIC_PORT;
		}
		try
		{
			$url = 'http://' . $elasticHost . ':' . $elasticPort . self::ELASTIC_HEALTH_CHECK;
			$elasticHealth = json_decode(KCurlWrapper::getContent($url), true);
			if(!isset($elasticHealth['status']) || $elasticHealth['status'] == 'red')
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

	public static function createFile($fileName)
	{
		$cmd = 'head -c 1M </dev/urandom >' . $fileName . ' & ';
		return self::execCmd($cmd);
	}

	protected static function execCmd($cmd)
	{
		$pid = null;
		KalturaLog::debug('EXEC: ' . $cmd);

		try
		{
			shell_exec($cmd);
			$pid = shell_exec('echo $!');
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

	public static function shouldNotifyError($healthCheckArray)
	{
		foreach ($healthCheckArray as $key => $value)
		{
			if ($value === 0)
			{
				return true;
			}
		}
		return false;
	}

}