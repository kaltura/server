<?php
class KalturaLog
{
	private static $_logger;
	private static $_initialized = false;
	private static $_instance = null;
	
    const EMERG   = Zend_Log::EMERG;
    const ALERT   = Zend_Log::ALERT;
    const CRIT    = Zend_Log::CRIT;
    const ERR     = Zend_Log::ERR;
    const WARN    = Zend_Log::WARN;
    const NOTICE  = Zend_Log::NOTICE;
    const INFO    = Zend_Log::INFO;
    const DEBUG   = Zend_Log::DEBUG;
	
	public static function getInstance ()
	{
		 if (!self::$_instance) 
		 	self::$_instance = new KalturaLog();
		 	
		 return self::$_instance;
	}
	
	public static function initLog(Zend_Config $config = null)
	{
		if (self::$_initialized)
			return;
			
		self::$_logger = KalturaLogFactory::getLogger($config);
		self::$_initialized = true;
	}
	
	public static function setLogger($logger)
	{
		self::$_logger = $logger;
		self::$_initialized = true;
	}
	
	static function log($message, $priority = self::NOTICE)
	{
		self::initLog();
		self::$_logger->log($message, $priority);
	}
	
	static function alert($message)
	{
		self::initLog();
		self::$_logger->log($message, self::ALERT);
	}

	static function crit($message)
	{
		self::initLog();
		self::$_logger->log($message, self::CRIT);
	}

	static function err($message)
	{
		self::initLog();
		self::$_logger->log($message, self::ERR);
	}	

	static function warning($message)
	{
		self::initLog();
		self::$_logger->log($message, self::WARN);
	}

	static function notice($message)
	{
		self::initLog();
		self::$_logger->log($message, self::NOTICE);
	}	

	static function info($message)
	{
		self::initLog();
		self::$_logger->log($message, self::INFO);
	}

	static function debug($message)
	{
		self::initLog();
		self::$_logger->log($message, self::DEBUG);
	}
	
	static function setContext($context)
	{
		self::initLog();
		self::$_logger->setEventItem("context", $context);
	}
}

class KalturaStdoutLogger
{
	public function log($message, $priority = KalturaLog::NOTICE)
	{
		echo "[" . date('Y-m-d H:i:s') . "]$message\n";
	}
}

class LogTime 
{
	public function __toString()
	{
		return date('Y-m-d H:i:s');
	}
}

class UniqueId
{
	static $_uniqueId = null;
	public function __toString()
	{
		if (self::$_uniqueId === null)
		{
			self::$_uniqueId = (string)rand();
			// add a the unique id to Apache's internal variable so we can later log it using the %{KalturaLog_UniqueId}n placeholder
			// within the LogFormat apache directive. This way each access_log record can be matched with its kaltura log lines
			if (function_exists('apache_note'))
				apache_note("KalturaLog_UniqueId", self::$_uniqueId);
		}
			
		return self::$_uniqueId;
	}
}

class LogMethod
{
	public function __toString()
	{
		$backtraceIndex = 7;
		$backtrace = debug_backtrace();
		if (isset($backtrace[$backtraceIndex]))
		{
			if (isset($backtrace[$backtraceIndex]["class"]))
				return $backtrace[$backtraceIndex]["class"].$backtrace[$backtraceIndex]["type"].$backtrace[$backtraceIndex]["function"];
			else
				return $backtrace[$backtraceIndex]["function"];
		}
		else 
		{
			return "global";
		}
	}
}

class LogIp
{
	static $_ip = null;
	public function __toString()
	{
		if (self::$_ip === null)
		{
			self::$_ip = (string)infraRequestUtils::getRemoteAddress();
		}
			
		return self::$_ip;
	}
}
?>