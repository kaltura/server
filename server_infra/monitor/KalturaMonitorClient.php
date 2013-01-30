<?php
/**
 * @package infra
 * @subpackage monitor
 */
class KalturaMonitorClient
{
	protected static $stream = null;
	protected static $hostname = null;
	
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
			self::$stream = fsockopen($host, $pathInfo['port'], $errno, $errstr, 10);
			if(self::$stream)
				return true;
			
			if(class_exists('KalturaLog'))
				KalturaLog::err("Open socket failed: $errstr");
		}
		
		self::$stream = fopen($uri, 'a');
		if(self::$stream)
			return true;
			
		return false;
	}
	
	protected static function getHostname()
	{
		if(self::$hostname)
			return self::$hostname;
		
		if(isset($_SERVER['HOSTNAME']))
			self::$hostname = $_SERVER['HOSTNAME'];
			
		if(is_null(self::$hostname))
			self::$hostname = gethostname();
			
		if(is_null(self::$hostname))
			self::$hostname = $_SERVER['SERVER_NAME'];
			
		if(is_null(self::$hostname))
			error_log('Host name is not defined, please define environment variable named HOSTNAME');
		
		return self::$hostname;
	}
	
	public static function monitor($cached, $action, $partnerId, $sessionType, $isInMultiRequest = false)
	{
		if(!self::$stream && !self::init())
			return false;	
			
		$data = array(
			'server'			=> self::getHostname(),
			'address'			=> infraRequestUtils::getRemoteAddress(),
			'partner'			=> $partnerId,
			'action'			=> $action,
			'cached'			=> $cached,
			'sessionType'		=> $sessionType,
			'inMultiRequest'	=> $isInMultiRequest,
		);
		
		if(class_exists('UniqueId'))
			$data['sessionId'] = UniqueId::get();
			
		$str = json_encode($data);
		fwrite(self::$stream, $str);
	}
}