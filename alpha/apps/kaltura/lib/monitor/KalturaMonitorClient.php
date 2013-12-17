<?php
/**
 * @package infra
 * @subpackage monitor
 */
class KalturaMonitorClient
{
	const MAX_PACKET_SIZE = 1400;
	
	const EVENT_API_START = 'start';
	const EVENT_API_END = 	'end';
	const EVENT_DATABASE = 	'db';
	const EVENT_SPHINX = 	'sphinx';
	
	const FIELD_EVENT_TYPE = 		'e';
	const FIELD_SERVER = 			's';
	const FIELD_UNIQUE_ID =			'u';
	const FIELD_IP_ADDRESS = 		'i';
	const FIELD_PARTNER_ID = 		'p';
	const FIELD_ACTION = 			'a';
	const FIELD_CACHED = 			'c';
	const FIELD_KS_TYPE = 			'k';
	const FIELD_CLIENT_TAG = 		'l';
	const FIELD_MULTIREQUEST = 		'm';
	const FIELD_EXECUTION_TIME = 	'x';
	const FIELD_ERROR_CODE = 		'r';
	const FIELD_DATABASE = 			'd';
	const FIELD_TABLE = 			't';
	const FIELD_QUERY_TYPE = 		'q';
	
	protected static $queryTypes = array(
			'SELECT ' 		=> 'SELECT',
			'UPDATE ' 		=> 'UPDATE',
			'INSERT INTO ' 	=> 'INSERT',
			'replace into ' => 'REPLACE');
	
	protected static $stream = null;
	
	protected static $basicEventInfo = array();
	protected static $basicApiInfo = array();
	protected static $apiStartTime = null;
	
	protected static $bufferedPacket = '';
	
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
	
	protected static function flushPacket()
	{
		if (!self::$bufferedPacket || !self::$stream)
			return;
		
		if (fwrite(self::$stream, self::$bufferedPacket) === false)
			self::$stream = false;		// failed - don't try to write any more data
		self::$bufferedPacket = '';
	} 
	
	protected static function writeDeferredEvent($data)
	{
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
	
	public static function monitorApiStart($cached, $action, $partnerId, $sessionType, $clientTag, $isInMultiRequest = false)
	{
		if (is_null(self::$stream))
			self::init();
		
		if (!self::$stream)
			return;
		
		self::$apiStartTime = microtime(true);

		self::$basicEventInfo = array(
			self::FIELD_SERVER			=> infraRequestUtils::getHostname(),
			self::FIELD_IP_ADDRESS		=> infraRequestUtils::getRemoteAddress(),
			self::FIELD_PARTNER_ID		=> $partnerId,
			self::FIELD_ACTION			=> $action,
		);
		
		if (!$cached)
		{
			require_once(__DIR__ . '/../../../../../infra/log/UniqueId.php');
			self::$basicEventInfo[self::FIELD_UNIQUE_ID] = UniqueId::get();
		}
		
		self::$basicApiInfo = array(
			self::FIELD_CACHED			=> $cached,
			self::FIELD_KS_TYPE			=> $sessionType,
			self::FIELD_CLIENT_TAG		=> $clientTag,
			self::FIELD_MULTIREQUEST 	=> $isInMultiRequest,
		);
		
		$data = array_merge(self::$basicEventInfo, self::$basicApiInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_API_START,
		));

		self::writeEvent($data);
	}
	
	public static function monitorApiEnd($errorCode)
	{
		if (!self::$stream)
			return;
		
		$data = array_merge(self::$basicEventInfo, self::$basicApiInfo, array(
			self::FIELD_EVENT_TYPE 		=> self::EVENT_API_END,
			self::FIELD_EXECUTION_TIME	=> (microtime(true) - self::$apiStartTime),
			self::FIELD_ERROR_CODE		=> $errorCode,
		));
	
		self::writeEvent($data);
	}
	
	public static function monitorDatabaseAccess($sql, $sqlTook, $hostName = null)
	{
		if (!self::$stream)
			return;
		
		// strip the comment
		if (kString::beginsWith($sql, '/*'))
		{
			$eventType = self::EVENT_DATABASE;
			$commentEndPos = strpos($sql, '*/') + 2;
			$comment = substr($sql, 0, $commentEndPos);			
			$matches = null;
			if (preg_match('~^/\* [^\]]+\[\d+\]\[([^\]]+)\] \*/~', $comment, $matches))
				$hostName = $matches[1];
			$sql = trim(substr($sql, $commentEndPos));
		}
		else
			$eventType = self::EVENT_SPHINX;

		// extract the query type
		$queryType = null;
		foreach (self::$queryTypes as $prefix => $curQueryType)
		{
			if (kString::beginsWith($sql, $prefix))
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
		));
		
		self::writeDeferredEvent($data);
	}
}