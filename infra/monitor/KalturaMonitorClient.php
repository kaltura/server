<?php
/**
 * @package infra
 * @subpackage monitor
 */
class KalturaMonitorClient
{
	const MAX_PACKET_SIZE = 1400;

	const EVENT_API_CACHE      = 'cache';
	const EVENT_API_START      = 'start';
	const EVENT_API_END        = 'end';
	const EVENT_DATABASE       = 'db';
	const EVENT_SPHINX         = 'sphinx';
	const EVENT_CONNTOOK       = 'conn';
	const EVENT_DUMPFILE       = 'file';
	const EVENT_ELASTIC        = 'elastic';
	const EVENT_DRUID          = 'druid';
	const EVENT_COUCHBASE      = 'couchbase';
	const EVENT_FILE_SYSTEM    = 'filesystem';
	const EVENT_MEMCACHE       = 'memcache';
	const EVENT_REDIS          = 'redis';
	const EVENT_CURL           = 'curl';
	const EVENT_AXEL           = 'axel';
	const EVENT_RABBIT         = 'rabbit';
	const EVENT_KAFKA          = 'kafka';
	const EVENT_SLEEP          = 'sleep';
	const EVENT_UPLOAD         = 'upload';
	const EVENT_EXEC           = 'exec';
	const EVENT_ERROR          = 'error';


	const FIELD_ACTION = 			'a';
	const FIELD_COUNT =				'c';
	const FIELD_DATABASE = 			'd';
	const FIELD_EVENT_TYPE = 		'e';
	const FIELD_FILE_PATH = 		'f';
	const FIELD_HOST =				'h';
	const FIELD_IP_ADDRESS = 		'i';
	const FIELD_KS_TYPE = 			'k';
	const FIELD_CLIENT_TAG = 		'l';
	const FIELD_MULTIREQUEST = 		'm';
	const FIELD_LENGTH =			'n';
	const FIELD_COMMAND =			'o';
	const FIELD_PARTNER_ID = 		'p';
	const FIELD_QUERY_TYPE = 		'q';
	const FIELD_ERROR_CODE = 		'r';
	const FIELD_SERVER = 			's';
	const FIELD_TABLE = 			't';
	const FIELD_UNIQUE_ID =			'u';
	const FIELD_EXECUTION_TIME = 	'x';
	const FIELD_FILE_SIZE = 		'z';
	const FIELD_ENV =    			'g';

	const SESSION_COUNTERS_SECRET_HEADER = 'HTTP_X_KALTURA_SESSION_COUNTERS';
	
	const SERVICE_OK = 'OK';
	const SERVICE_NEARING_LIMITS = 'NearingLimits';
	const DEFAULT_SERVICE_THRESHOLD = 1; // 1 second
	const DEFAULT_SERVICE_CACHE_EXPIRY = 70; // 70 second
	const DEFAULT_CACHE_BUCKET_INTERVAL_SECONDS = 10; // 70 second
	const DEFAULT_HISTORICAL_BUCKET_COUNT = 5; // 5 * DEFAULT_BUCKET_SIZE_SECONDS
	const DEFAULT_MIN_REQUIRED_BUCKETS = 2; //
	
	protected static $queryTypes = array(
			'SELECT ' 		=> 'SELECT',
			'UPDATE ' 		=> 'UPDATE',
			'INSERT INTO ' 	=> 'INSERT',
			'replace into ' => 'REPLACE');
	
	protected static $stream = null;
	
	protected static $basicEventInfo = array();
	protected static $basicApiInfo = array();
	protected static $lastTime = null;
	
	protected static $sleepTime = 0;
	protected static $sleepCount = 0;

	protected static $wroteUpload = false;

	protected static $bufferedPacket = '';

	static protected $sessionCounters = array (
		self::EVENT_DATABASE    =>  0,
		self::EVENT_SPHINX      =>  0,
		self::EVENT_COUCHBASE   =>  0,
		self::EVENT_ELASTIC     =>  0,
		self::EVENT_DRUID       =>  0,
		self::EVENT_FILE_SYSTEM =>  0
	);

	public static function prettyPrintCounters()
	{
		$serviceInfo = kCurrentContext::$isInMultiRequest ?  ' S:multiRequest A:null' : ' S:' . kCurrentContext::$service . ' A:' . kCurrentContext::$action;
		$str='pid:' . kCurrentContext::getCurrentPartnerId() . $serviceInfo . ' ';
		foreach (self::$sessionCounters as $key => $value)
		{
			$str .= $key . ':' . $value . ' ';
		}
		return $str;
	}

	public static function monitorRequestEnd()
	{
		KalturaLog::info('Session counters ' . self::prettyPrintCounters());

		if(!isset ($_SERVER[self::SESSION_COUNTERS_SECRET_HEADER]))
		{
			return;
		}

		$sessionCountersShardSecret = kConf::get('SESSION_COUNTERS_SECRET', 'local', null);
		list ($clientRequestTime,$hash) = explode(',', $_SERVER[self::SESSION_COUNTERS_SECRET_HEADER]);

		if($sessionCountersShardSecret && $clientRequestTime && $hash)
		{
			if(abs(time() - $clientRequestTime) < 300)
			{
				if($hash === hash('sha256', "$clientRequestTime,$sessionCountersShardSecret"))
				{
					header('X-Kaltura-session-counters: ' . base64_encode(json_encode(self::$sessionCounters)));
				}
			}
		}
	}


	protected static function init()
	{
		if(!kConf::hasParam('monitor_uri'))
			return null;

		$uri = kConf::get('monitor_uri');
		$pathInfo = parse_url($uri);
		if(isset($pathInfo['host']) && $pathInfo['port'])
		{
			$host = $pathInfo['host'];
			if(isset($pathInfo['scheme']))
				$host = $pathInfo['scheme'] . "://$host";

			$errno = null;
			$errstr = null;
			self::$stream = fsockopen($host, $pathInfo['port'], $errno, $errstr, 1);
			if(self::$stream)
				return true;

			if(class_exists('KalturaLog'))
				KalturaLog::err("Open socket failed: $errstr");
		}

		self::$stream = fopen($uri, 'a');
		if(self::$stream)
			return true;

		self::$stream = false;		// prevent init from being called again
		return false;
	}
	
	public static function flushPacket()
	{
		if (!self::$bufferedPacket || !self::$stream)
			return;
		
		if (fwrite(self::$stream, self::$bufferedPacket) === false)
			self::$stream = false;		// failed - don't try to write any more data
		self::$bufferedPacket = '';
	} 
	
	protected static function writeDeferredEvent($data)
	{
		$eventType = $data[self::FIELD_EVENT_TYPE];
		if(isset(self::$sessionCounters[$eventType]))
		{
			self::$sessionCounters[$eventType]++;
		}

		$str = json_encode($data);
		if (strlen($str) > self::MAX_PACKET_SIZE)
			return;
		
		if (strlen(self::$bufferedPacket) + strlen($str) > self::MAX_PACKET_SIZE)
			self::flushPacket();

		if (self::$bufferedPacket)
			self::$bufferedPacket .= "\0";			
		self::$bufferedPacket .= $str;
	}

	protected static function writeEvent($data)
	{
		self::writeDeferredEvent($data);
		self::flushPacket();
	}

	protected static function getApiExecTime()
	{
		$currentTime = microtime(true);
		$result = $currentTime - self::$lastTime;
		self::$lastTime = $currentTime;

		return $result;
	}
	
	protected static function flushEvents()
	{
		if (class_exists('kInfraMemcacheCacheWrapper'))
		{
			kInfraMemcacheCacheWrapper::sendMonitorEvents();
		}

		if (class_exists('kInfraRedisCacheWrapper'))
		{
			kInfraRedisCacheWrapper::sendMonitorEvents();
		}

		if (self::$sleepCount > 0)
		{
			$data = array_merge(self::$basicEventInfo, array(
					self::FIELD_EVENT_TYPE 		=> self::EVENT_SLEEP,
					self::FIELD_EXECUTION_TIME	=> self::$sleepTime,
					self::FIELD_COUNT			=> self::$sleepCount,
			));

			self::writeDeferredEvent($data);

			self::$sleepTime = 0;
			self::$sleepCount = 0;
		}
	}

	public static function initApiMonitor($cached, $action, $partnerId, $clientTag = null)
	{
		if (is_null(self::$stream))
			self::init();
		
		if (!self::$stream)
			return;

		if (!self::$lastTime)
		{
			self::$lastTime = isset($GLOBALS['start']) ? $GLOBALS['start'] : microtime(true);
		}
		
		self::$basicEventInfo = array(
			self::FIELD_SERVER			=> infraRequestUtils::getHostname(),
			self::FIELD_IP_ADDRESS		=> infraRequestUtils::getRemoteAddress(),
			self::FIELD_PARTNER_ID		=> strval($partnerId),
			self::FIELD_ACTION			=> $action,
			self::FIELD_CLIENT_TAG		=> strval($clientTag),
		);
		
		$envName = getenv('ENV_NAME');
		if($envName)
		{
			self::$basicEventInfo[self::FIELD_ENV] = $envName;
		}
		
		if (!$cached)
		{
			require_once(__DIR__ . '/../log/UniqueId.php');
			self::$basicEventInfo[self::FIELD_UNIQUE_ID] = UniqueId::get();
		}
	}
	
	public static function monitorApiStart($cached, $action, $partnerId, $sessionType = null, $clientTag = null, $isInMultiRequest = false)
	{
		if (!$partnerId)
		{
			$partnerId = preg_match('#/p/(\d+)/#', $_SERVER['REQUEST_URI'], $matches) ? $matches[1] : null;
		}

		if ($partnerId == -1)		// cannot use BATCH_PARTNER_ID since this may run before the autoloader
		{
			$splittedClientTag = explode(' ', $clientTag);
			$partnerIdIndex = array_search('partnerId:', $splittedClientTag);
			if ($partnerIdIndex !== false && isset($splittedClientTag[$partnerIdIndex + 1]))
			{
				$partnerId = $splittedClientTag[$partnerIdIndex + 1];
			}
		}
		
		self::initApiMonitor($cached, $action, $partnerId, $clientTag);
		
		if (!self::$stream)
			return;
		
		self::$basicApiInfo = array(
			self::FIELD_KS_TYPE			=> strval($sessionType),
			self::FIELD_MULTIREQUEST 	=> $isInMultiRequest ? '1' : '0',
		);
		
		$data = array_merge(self::$basicEventInfo, self::$basicApiInfo);

		if ($cached)
		{
			self::flushEvents();

			$data[self::FIELD_EVENT_TYPE] = self::EVENT_API_CACHE;
			$data[self::FIELD_EXECUTION_TIME] = self::getApiExecTime();
			self::writeEvent($data);
			return;
		}

		$data[self::FIELD_EVENT_TYPE] = self::EVENT_API_START;
		self::writeDeferredEvent($data);

		if (count($_FILES) > 0)
		{
			self::monitorUpload();
		}
	}
	
	public static function monitorPs2Start()
	{
		$context = sfContext::getInstance();
		$request = $context->getRequest();

		$module = $request->getParameter('module');
		$action = $module . '.' . $request->getParameter('action');
		switch (strtolower($action))
		{
		case 'extwidget.playmanifest':
			return;		// handled by kApiCache

		case 'partnerservices2.defpartnerservices2base':
			$realAction = $request->getParameter('myaction');
			if ($realAction)
			{
				$action = $module . '.' . $realAction;
			}
			break;
		}

		$params = infraRequestUtils::getRequestParams();
		$sessionType = isset($params['ks']) ? kSessionBase::SESSION_TYPE_USER : kSessionBase::SESSION_TYPE_NONE;	// assume user ks
		$clientTag = isset($params['clientTag']) ? $params['clientTag'] : null;
		$partnerId = isset($params['partner_id']) && ctype_digit($params['partner_id']) ? $params['partner_id'] : null;

		self::monitorApiStart(false, $action, $partnerId, $sessionType, $clientTag);
		
		list($service, $action) = explode('.', $action, 2);
		self::checkApiRateLimit($partnerId, $service, $action, $params);
	}

	public static function monitorApiEnd($errorCode, $took = null)
	{
		self::sendServiceStatusHeader($took);
		
		if (!self::$stream)
			return;

		self::flushEvents();

		$data = array_merge(self::$basicEventInfo, self::$basicApiInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_API_END,
			self::FIELD_EXECUTION_TIME	=> self::getApiExecTime(),
		));

		if ($errorCode)
		{
			$data[self::FIELD_ERROR_CODE] = strval($errorCode);
		}

		self::writeEvent($data);
	}

	protected static function monitorUpload()
	{
		if (self::$wroteUpload)
		{
			return;
		}

		self::$wroteUpload = true;

		$size = 0;
		$errorCode = null;
		foreach ($_FILES as $curFile)
		{
			if (is_numeric($curFile['size']))
			{
				$size += $curFile['size'];
			}

			if ($curFile['error'])
			{
				$errorCode = 'UPLOAD_' . $curFile['error'];
			}
		}

		$requestTime = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : $_SERVER['REQUEST_TIME'];

		$data = array_merge(self::$basicEventInfo, self::$basicApiInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_UPLOAD,
			self::FIELD_FILE_SIZE		=> $size,
			self::FIELD_EXECUTION_TIME	=> self::$lastTime - $requestTime,
		));

		if ($errorCode)
		{
			$data[self::FIELD_ERROR_CODE] = $errorCode;
		}

		self::writeDeferredEvent($data);
	}

	protected static function getDsnHost($dsn)
	{
		$hostStart = strpos($dsn, 'host=');
		if ($hostStart === false)
		{
			return $dsn;
		}

		$hostStart += 5;
		$hostEnd = strpos($dsn, ';', $hostStart);
		if ($hostEnd === false)
		{
			return $dsn;
		}

		return substr($dsn, $hostStart, $hostEnd - $hostStart);
	}

	public static function monitorDatabaseAccess($sql, $sqlTook, $hostName = null)
	{
		if (!self::$stream)
			return;

		// strip the comment
		if (substr($sql, 0, 2) == '/*')
		{
			$eventType = self::EVENT_DATABASE;
			$commentEndPos = strpos($sql, '*/') + 2;
			$comment = substr($sql, 0, $commentEndPos);			
			$matches = null;
			if (preg_match('~^/\* [^\]]+\[\d+\]\[([^\]]+)\] \*/~', $comment, $matches))
			{
				$hostName = $matches[1];

				$config = kConf::getDB();
				if (isset($config['datasources'][$hostName]['connection']['dsn']))
				{
					$hostName = self::getDsnHost($config['datasources'][$hostName]['connection']['dsn']);
				}
			}
			$sql = trim(substr($sql, $commentEndPos));
		}
		else
			$eventType = self::EVENT_SPHINX;

		// extract the query type
		$queryType = null;
		foreach (self::$queryTypes as $prefix => $curQueryType)
		{
			if (substr($sql, 0, strlen($prefix)) == $prefix)
			{
				$sql = substr($sql, strlen($prefix));
				$queryType = $curQueryType;
				break;
			}
		}
		
		if (!$queryType)
			return;

		// extract the table name 
		$tableNameStart = 0;
		if ($queryType == 'SELECT')
		{
			$fromPos = strpos($sql, ' FROM ');
			if ($fromPos === false)
				return;
			$tableNameStart = $fromPos + 6;
		}
		
		$tableNameEnd = strpos($sql, ' ', $tableNameStart);
		if ($tableNameEnd === false)
			return;
		$tableName = substr($sql, $tableNameStart, $tableNameEnd - $tableNameStart);
		$tableName = str_replace('`', '', $tableName);
		
		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> $eventType,
			self::FIELD_DATABASE		=> $hostName,
			self::FIELD_TABLE			=> $tableName,
			self::FIELD_QUERY_TYPE		=> $queryType,
			self::FIELD_EXECUTION_TIME	=> $sqlTook,			
			self::FIELD_LENGTH			=> strlen($sql),
		));
		
		self::writeDeferredEvent($data);
	}

	public static function monitorElasticAccess($actionName, $indexName, $body, $queryTook, $hostName, $errorCode)
	{
		if (!self::$stream)
			return;

		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_ELASTIC,
			self::FIELD_DATABASE		=> $hostName,
			self::FIELD_TABLE			=> $indexName,
			self::FIELD_QUERY_TYPE		=> $actionName,
			self::FIELD_EXECUTION_TIME	=> $queryTook,
			self::FIELD_LENGTH			=> strlen(strval($body)),
		));

		if ($errorCode)
		{
			$data[self::FIELD_ERROR_CODE] = $errorCode;
		}

		self::writeDeferredEvent($data);
	}

	public static function monitorDruidQuery($hostName, $dataSource, $queryType, $querySize, $queryTook, $errorCode)
	{
		if (!self::$stream)
			return;

		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_DRUID,
			self::FIELD_DATABASE		=> $hostName,
			self::FIELD_TABLE			=> $dataSource,
			self::FIELD_QUERY_TYPE		=> $queryType,
			self::FIELD_LENGTH			=> $querySize,
			self::FIELD_EXECUTION_TIME	=> $queryTook,
		));

		if ($errorCode)
		{
			$data[self::FIELD_ERROR_CODE] = $errorCode;
		}

		self::writeDeferredEvent($data);
	}

	public static function monitorCouchBaseAccess($dataSource, $bucketName, $queryType, $queryTook, $querySize)
	{
		if (!self::$stream)
			return;

		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_COUCHBASE,
			self::FIELD_DATABASE		=> $dataSource,
			self::FIELD_TABLE			=> $bucketName,
			self::FIELD_QUERY_TYPE		=> $queryType,
			self::FIELD_EXECUTION_TIME	=> $queryTook,
			self::FIELD_LENGTH			=> $querySize,
		));

		self::writeDeferredEvent($data);
	}

	public static function monitorConnTook($dsn, $connTook, $count=1, $errorCode='')
	{
		if (!self::$stream)
			return;
		
		$data = array_merge(self::$basicEventInfo, array(
				self::FIELD_EVENT_TYPE 		=> self::EVENT_CONNTOOK,
				self::FIELD_DATABASE		=> self::getDsnHost($dsn),
				self::FIELD_EXECUTION_TIME	=> $connTook,
				self::FIELD_COUNT			=> $count,
		));

		if ($errorCode)
		{
			$data[self::FIELD_ERROR_CODE] = $errorCode;
		}
		
		self::writeDeferredEvent($data);		
	}

	public static function monitorMemcacheAccess($hostName, $timeTook, $count, $errorCode)
	{
		if (!self::$stream)
			return;

		$data = array_merge(self::$basicEventInfo, array(
				self::FIELD_EVENT_TYPE 		=> self::EVENT_MEMCACHE,
				self::FIELD_DATABASE		=> $hostName,
				self::FIELD_EXECUTION_TIME	=> $timeTook,
				self::FIELD_COUNT			=> $count,
		));

		if ($errorCode)
		{
			$data[self::FIELD_ERROR_CODE] = $errorCode;
		}

		self::writeDeferredEvent($data);
	}
	
	public static function monitorRedisAccess($hostName, $timeTook, $count)
	{
		if (!self::$stream)
			return;
		
		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_REDIS,
			self::FIELD_DATABASE		=> $hostName,
			self::FIELD_EXECUTION_TIME	=> $timeTook,
			self::FIELD_COUNT			=> $count,
		));
		
		self::writeDeferredEvent($data);
	}

	public static function monitorCurl($hostName, $timeTook, $curlHandle=null)
	{
		if (!self::$stream)
			return;

		$data = array_merge(self::$basicEventInfo, array(
				self::FIELD_EVENT_TYPE 		=> self::EVENT_CURL,
				self::FIELD_HOST			=> $hostName,
				self::FIELD_EXECUTION_TIME	=> $timeTook,
		));

		if ($curlHandle)
		{
			$errno = curl_errno($curlHandle);
			$httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
			if ($errno)
			{
				$data[self::FIELD_ERROR_CODE] = 'CURL_' . $errno;
			}
			else if ($httpCode < 200 || $httpCode >= 300)
			{
				$data[self::FIELD_ERROR_CODE] = 'HTTP_' . $httpCode;
			}
		}

		self::writeDeferredEvent($data);
	}

	public static function monitorAxel($hostName, $timeTook, $errorCode = null)
	{
		if (!self::$stream)
			return;
		
		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_AXEL,
			self::FIELD_HOST			=> $hostName,
			self::FIELD_EXECUTION_TIME	=> $timeTook,
		));
		
		if ($errorCode)
		{
			$data[self::FIELD_ERROR_CODE] = $errorCode;
		}
		
		self::writeDeferredEvent($data);
	}

	public static function monitorFileSystemAccess($operation, $timeTook, $errorCode)
	{
		if (!self::$stream)
			return;
		
		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_FILE_SYSTEM,
			self::FIELD_EXECUTION_TIME	=> $timeTook,
			self::FIELD_QUERY_TYPE		=> $operation,
		));

		if ($errorCode)
		{
			$data[self::FIELD_ERROR_CODE] = $errorCode;
		}

		self::writeDeferredEvent($data);
	}
	
	protected static function getRangeLength($size)
	{		
		if (!isset($_SERVER['HTTP_RANGE']))
			return $size;
				
		list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
		
		if (strpos($range, ',') !== false)
			return null;		// ignore multibyte range
	
		$end = $size - 1;
		if ($range[0] == '-')
		{
			// The n-number of the last bytes is requested
			$start = $size - substr($range, 1);
		}
		else
		{
			$range  = explode('-', $range);
			$start = $range[0];
			if (isset($range[1]) && is_numeric($range[1]))
				$end = min($end, $range[1]);
		}
	
		if ($start > $end || $start > $size - 1 || $end >= $size)
			return null;		// invalid range

		return $end - $start + 1;
	}
	
	public static function monitorDumpFile($fileSize, $filePath)
	{
		if (!self::$stream)
			return;
		
		$fileSize = self::getRangeLength($fileSize);
		if (is_null($fileSize))
			return;
		
		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 	=> self::EVENT_DUMPFILE,
			self::FIELD_FILE_SIZE	=> $fileSize,
			self::FIELD_FILE_PATH	=> $filePath,
		));
		
		self::writeEvent($data);
	}

	public static function monitorRabbitAccess($dataSource, $queryType, $queryTook, $tableName = null, $querySize = null, $errorCode = '')
	{
		if (!self::$stream)
			return;

		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_RABBIT,
			self::FIELD_DATABASE		=> $dataSource,
			self::FIELD_TABLE			=> $tableName,
			self::FIELD_QUERY_TYPE		=> $queryType,
			self::FIELD_EXECUTION_TIME	=> $queryTook,
			self::FIELD_LENGTH			=> $querySize ? $querySize : 0,
		));

		if ($errorCode)
		{
			$data[self::FIELD_ERROR_CODE] = 'AMQP_' . $errorCode;
		}

		self::writeDeferredEvent($data);
	}

	public static function monitorKafkaAccess($dataSource, $queryType, $queryTook, $tableName = null, $querySize = null, $errorCode = '')
	{
		if (!self::$stream)
		{
			return;
		}

		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_KAFKA,
			self::FIELD_DATABASE		=> $dataSource,
			self::FIELD_TABLE			=> $tableName,
			self::FIELD_QUERY_TYPE		=> $queryType,
			self::FIELD_EXECUTION_TIME	=> $queryTook,
			self::FIELD_LENGTH			=> $querySize ? $querySize : 0,
			));

		if ($errorCode)
		{
			$data[self::FIELD_ERROR_CODE] = 'KAFKA_' . $errorCode;
		}

		self::writeDeferredEvent($data);
	}

	public static function sleep($sec)
	{
		sleep($sec);

		self::$sleepTime += $sec;
		self::$sleepCount++;
	}

	public static function usleep($micros)
	{
		usleep($micros);

		self::$sleepTime += $micros / 1000000;
		self::$sleepCount++;
	}

	public static function monitorExec($command, $startTime, $errorCode='')
	{
		if (!self::$stream)
			return;

		$spacePos = strpos($command, ' ');
		if ($spacePos !== false)
		{
			$command = substr($command, 0, $spacePos);
		}

		$command = trim($command, "'\"");
		$command = basename($command);

		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_EXEC,
			self::FIELD_COMMAND			=> $command,
			self::FIELD_EXECUTION_TIME	=> microtime(true) - $startTime,
		));

		if ($errorCode)
		{
			$data[self::FIELD_ERROR_CODE] = $errorCode;
		}

		self::writeDeferredEvent($data);
	}
	
	public static function sendErrorEvent($errorCode)
	{
		if (!self::$stream)
			return;
		
		$data = array_merge(self::$basicEventInfo, self::$basicApiInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_ERROR,
			self::FIELD_ERROR_CODE	    => $errorCode,
		));
		
		self::writeDeferredEvent($data);
	}
	
	public static function checkApiRateLimit($partnerId, $service, $action, $params)
	{
		if(!isset($partnerId))
		{
			return;
		}
		
		$params['service'] = $service;
		$params['action'] = $action;
		
		if(!KalturaResponseCacher::rateLimit($service, $action, $params, $partnerId))
		{
			KExternalErrors::dieError(KExternalErrors::ACTION_RATE_LIMIT);
		}
	}
	
	private static function sendServiceStatusHeader($requestTook = null)
	{
		if(!isset($requestTook) || !kApcWrapper::functionExists('inc'))
			return;
		
		$serviceStatusConfig = kConf::get('service_status_config', kConfMapNames::RUNTIME_CONFIG, array());
		if(!is_array($serviceStatusConfig) || !count($serviceStatusConfig))
			return;
		
		if(!isset($serviceStatusConfig['enabled']) || !$serviceStatusConfig['enabled'])
			return;
		
		$skipActions = isset($serviceStatusConfig['skip_actions']) ?  $serviceStatusConfig['skip_actions'] : array();
		if(isset(self::$basicEventInfo[self::FIELD_ACTION]) && in_array(self::$basicEventInfo[self::FIELD_ACTION], $skipActions))
		{
			self::safeLog("Skipping service status for action: " . self::$basicEventInfo[self::FIELD_ACTION]);
			return;
		}
		
		$thresholdInSeconds = $serviceStatusConfig['threshold_in_seconds'] ?? self::DEFAULT_SERVICE_THRESHOLD;
		$cacheExpiry = $serviceStatusConfig['cache_expiry'] ?? self::DEFAULT_SERVICE_CACHE_EXPIRY;
		
		$cacheBucketInterval = $serviceStatusConfig['bucket_interval_in_seconds'] ?? self::DEFAULT_CACHE_BUCKET_INTERVAL_SECONDS;
		$historicalBucketsToFetch = $serviceStatusConfig['historical_bucket_count'] ?? self::DEFAULT_HISTORICAL_BUCKET_COUNT;
		$minimumRequiredBuckets = $serviceStatusConfig['minimum_require_buckets'] ?? self::DEFAULT_MIN_REQUIRED_BUCKETS;
		$sendAnalyticsBeacons = $serviceStatusConfig['send_analytics_beacons'] ?? false;
		$sendHeader = $serviceStatusConfig['send_header'] ?? false;
		
		list($reqTime, $reqCount, $reqAvgTime) = self::getServiceStatusStats($requestTook, $cacheExpiry, $cacheBucketInterval, $historicalBucketsToFetch, $minimumRequiredBuckets);
		
		if($reqAvgTime)
		{
			$serviceStatus = self::SERVICE_OK;
			if($reqAvgTime > $thresholdInSeconds)
			{
				$serviceStatus = self::SERVICE_NEARING_LIMITS;
				if($sendAnalyticsBeacons)
				{
					self::sendErrorEvent('NEARING_LIMITS');
				}
			}
			
			if($sendHeader)
			{
				header('X-Kaltura-Service-Status: ' . $serviceStatus);
			}
			
			self::safeLog("Service status: serviceStatus [$serviceStatus] count [$reqCount] time [$reqTime] avg [$reqAvgTime]");
		}
	}
	
	private static function getServiceStatusStats($requestTook, $cacheExpiry, $cacheBucketInterval, $historicalBucketsToFetch, $minimumRequiredBuckets)
	{
		// convert to micro seconds
		$requestTook = (int)round($requestTook * 1000000);
		
		$currentCacheKeyPostfix = intval(time()/$cacheBucketInterval);
		$reqCount = kApcWrapper::apcInc('req_count_'.$currentCacheKeyPostfix, 1, null, $cacheExpiry);
		$reqTime = kApcWrapper::apcInc('req_time_'.$currentCacheKeyPostfix, $requestTook, null, $cacheExpiry);
		if($reqCount === false || $reqTime === false)
		{
			return array(null, null, null);
		}
		
		// get last 5 10 seconds buckets as well each key is a 10 second interval
		// so we get last 50 seconds data
		$keysToFetch = array();
		for($i=1; $i<=$historicalBucketsToFetch; $i++)
		{
			$keysToFetch[] = 'req_count_'.($currentCacheKeyPostfix - $i);
			$keysToFetch[] = 'req_time_'.($currentCacheKeyPostfix - $i);
		}
		
		$res = kApcWrapper::apcMultiGet($keysToFetch);
		if(!is_array($res) || count($res) < ($minimumRequiredBuckets*2))
		{
			//If we dont have enough historical data yet - we return null to avoid false positives
			return array(null, null, null);
		}
		
		foreach($res as $key => $value)
		{
			if(strpos($key, 'req_count_') === 0 && is_numeric($value))
			{
				$reqCount += $value;
			}
			else if(strpos($key, 'req_time_') === 0 && is_numeric($value))
			{
				$reqTime += $value;
			}
		}
		
		return array($reqTime, $reqCount, ($reqTime/1000000)/$reqCount);
	}
	
	protected static function safeLog($msg)
	{
		if (class_exists('KalturaLog') && KalturaLog::isInitialized())
		{
			KalturaLog::debug($msg);
		}
	}
}
