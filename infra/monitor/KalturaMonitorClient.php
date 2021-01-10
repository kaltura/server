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
	const EVENT_CURL           = 'curl';
	const EVENT_RABBIT         = 'rabbit';
	const EVENT_SLEEP          = 'sleep';
	const EVENT_UPLOAD         = 'upload';
	const EVENT_EXEC           = 'exec';


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

	const SESSION_COUNTERS_SECRET_HEADER = 'HTTP_X_KALTURA_SESSION_COUNTERS';
	
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
		$action = $request->getParameter('module') . '.' . $request->getParameter('action');
		if (strtolower($action) == 'extwidget.playmanifest')
		{
			return;		// handled by kApiCache
		}

		$params = infraRequestUtils::getRequestParams();
		$sessionType = isset($params['ks']) ? kSessionBase::SESSION_TYPE_USER : kSessionBase::SESSION_TYPE_NONE;	// assume user ks
		$clientTag = isset($params['clientTag']) ? $params['clientTag'] : null;

		self::monitorApiStart(false, $action, null, $sessionType, $clientTag);
	}

	public static function monitorApiEnd($errorCode)
	{
		if (!self::$stream)
			return;

		self::flushEvents();

		$data = array_merge(self::$basicEventInfo, self::$basicApiInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_API_END,
			self::FIELD_EXECUTION_TIME	=> self::getApiExecTime(),
			self::FIELD_ERROR_CODE		=> strval($errorCode),
		));

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
				$errorCode = strval($curFile['error']);
			}
		}

		$requestTime = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : $_SERVER['REQUEST_TIME'];

		$data = array_merge(self::$basicEventInfo, self::$basicApiInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_UPLOAD,
			self::FIELD_FILE_SIZE		=> $size,
			self::FIELD_EXECUTION_TIME	=> self::$lastTime - $requestTime,
			self::FIELD_ERROR_CODE		=> $errorCode,
		));

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

	public static function monitorElasticAccess($actionName, $indexName, $body, $queryTook, $hostName = null)
	{
		if (!self::$stream)
			return;

		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_ELASTIC,
			self::FIELD_DATABASE		=> $hostName,
			self::FIELD_TABLE			=> $indexName,
			self::FIELD_QUERY_TYPE		=> $actionName,
			self::FIELD_EXECUTION_TIME	=> $queryTook,
			self::FIELD_LENGTH			=> strlen($body),
		));

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
			self::FIELD_ERROR_CODE		=> $errorCode,
		));

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

	public static function monitorConnTook($dsn, $connTook, $count=1)
	{
		if (!self::$stream)
			return;
		
		$data = array_merge(self::$basicEventInfo, array(
				self::FIELD_EVENT_TYPE 		=> self::EVENT_CONNTOOK,
				self::FIELD_DATABASE		=> self::getDsnHost($dsn),
				self::FIELD_EXECUTION_TIME	=> $connTook,
				self::FIELD_COUNT			=> $count,
		));
		
		self::writeDeferredEvent($data);		
	}

	public static function monitorMemcacheAccess($hostName, $timeTook, $count)
	{
		if (!self::$stream)
			return;

		$data = array_merge(self::$basicEventInfo, array(
				self::FIELD_EVENT_TYPE 		=> self::EVENT_MEMCACHE,
				self::FIELD_DATABASE		=> $hostName,
				self::FIELD_EXECUTION_TIME	=> $timeTook,
				self::FIELD_COUNT			=> $count,
		));

		self::writeDeferredEvent($data);
	}

	public static function monitorCurl($hostName, $timeTook)
	{
		if (!self::$stream)
			return;

		$data = array_merge(self::$basicEventInfo, array(
				self::FIELD_EVENT_TYPE 		=> self::EVENT_CURL,
				self::FIELD_HOST			=> $hostName,
				self::FIELD_EXECUTION_TIME	=> $timeTook,
		));

		self::writeDeferredEvent($data);
	}

	public static function monitorFileSystemAccess($operation, $timeTook, $execStatus)
	{
		if (!self::$stream)
			return;
		
		$data = array_merge(self::$basicEventInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_FILE_SYSTEM,
			self::FIELD_EXECUTION_TIME	=> $timeTook,
			self::FIELD_QUERY_TYPE		=> $operation,
			self::FIELD_ERROR_CODE 		=> $execStatus,
		));
		
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

	public static function monitorRabbitAccess($dataSource, $queryType, $queryTook, $tableName = null, $querySize = null, $errorType = '')
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
			self::FIELD_ERROR_CODE		=> $errorType,
		));

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

	public static function monitorExec($command, $startTime)
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

		self::writeDeferredEvent($data);
	}
}
