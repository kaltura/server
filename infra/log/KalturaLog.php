<?php
/**
 * @package infra
 * @subpackage log
 */
class KalturaLog
{
	private static $_logger;
	private static $_initialized = false;
	private static $_instance = null;
	private static $_enableTests = false;
	
	const EMERG   = Zend_Log::EMERG;
	const ALERT   = Zend_Log::ALERT;
	const CRIT    = Zend_Log::CRIT;
	const ERR     = Zend_Log::ERR;
	const WARN    = Zend_Log::WARN;
	const NOTICE  = Zend_Log::NOTICE;
	const INFO    = Zend_Log::INFO;
	const DEBUG   = Zend_Log::DEBUG;

	const LOG_TYPE_ANALYTICS = 'LOG_TYPE_ANALYTICS';
	const STANDARD_ERROR = 'STANDARD_ERROR';

	static protected $sessionCounters = array (
	    self::EMERG => 0,
        self::ALERT => 0,
        self::CRIT => 0,
        self::ERR => 0,
        self::WARN => 0,
        self::NOTICE => 0,
        self::INFO => 0,
        self::DEBUG => 0 );

	const COUNTERS_PREFIX = '_LOG_COUNTERS_';
	public static function storeCounters()
	{
		foreach (self::$sessionCounters as $key => $value)
		{
			if($value)
			{
				$success = true;
				apc_inc(self::COUNTERS_PREFIX . $key, $value, $success);
				if(!$success)
				{
					apc_store(self::COUNTERS_PREFIX . $key, $value);
				}
			}
		}
	}

	public static function getStoredCounters($shouldClear)
	{
		$counters = array();
		foreach (self::$sessionCounters as $key => $dummy)
		{
			$value = apc_fetch(self::COUNTERS_PREFIX . $key);
			$counters[" $key "] = $value ? $value : 0;
			if($shouldClear)
			{
				apc_delete(self::COUNTERS_PREFIX . $key);
			}
		}
		return $counters;
	}

	protected static function increaseLogCounters($priority)
	{
		if(isset(self::$sessionCounters[$priority]))
		{
			self::$sessionCounters[$priority]++;
		}
		else
		{
			self::$sessionCounters[$priority]=1;
		}
	}

	public static function isInitialized()
	{
		return 	self::$_initialized;
	}
	
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
		
		self::$_enableTests = isset($config->enableTests) ? $config->enableTests : false;
		
		self::$_logger = KalturaLogFactory::getLogger($config);
		self::$_initialized = true;
	}
	
	public static function getLogger()
	{
		return self::$_logger;
	}
	
	public static function setLogger($logger)
	{
		self::$_logger = $logger;
		self::$_initialized = true;
	}
	
	static function log($message, $priority = self::NOTICE)
	{
		self::initLog();
		self::callLogger($message, $priority);
	}

	static function alert($message)
	{
		self::initLog();
		if(!$message instanceof Exception)
			$message = new Exception($message);
		self::callLogger($message, self::ALERT);
	}

	static function crit($message)
	{
		self::initLog();
		if(!$message instanceof Exception)
			$message = new Exception($message);

		self::callLogger($message, self::CRIT);
	}

	static function err($message)
	{
		self::initLog();
		if(!$message instanceof Exception)
			$message = new Exception($message);

		self::callLogger($message, self::ERR);
	}

	static function warning($message)
	{
		self::initLog();
		self::callLogger($message, self::WARN);
	}

	static function notice($message)
	{
		self::initLog();
		self::callLogger($message, self::NOTICE);
	}

	static function info($message)
	{
		self::initLog();
		self::callLogger($message, self::INFO);
	}

	static function debug($message)
	{
		self::initLog();
		self::callLogger($message, self::DEBUG);
	}

	static protected function callLogger($message,$priority)
	{
		self::$_logger->log($message, $priority);
		self::increaseLogCounters($priority);
	}

	static function analytics(array $data)
	{
		$message = '';
		foreach ($data as $value)
		{
			$message .= strtr($value, ',', ' ') . ',';
		}
		$message = substr($message, 0, -1);
		self::logByType($message, self::LOG_TYPE_ANALYTICS, self::NOTICE);
	}

	static function stderr($message, $priority = self::ERR)
	{
		self::logByType($message, self::STANDARD_ERROR, $priority);
	}
	
	static function logByType($message, $type, $priority = self::DEBUG)
	{
		self::initLog();
		
		//check if this is a zend log (and not a sfLogger)
		if (get_class(self::$_logger) == 'Zend_Log')		
			self::$_logger->setEventItem("type", $type);
			
		self::$_logger->log($message, $priority);
		
		if (get_class(self::$_logger) == 'Zend_Log')
			self::$_logger->setEventItem("type", '');
	}
	
	static function setContext($context)
	{
		self::initLog();
		self::$_logger->setEventItem("context", $context);
	}
	
	static function getEnableTests()
	{
		return self::$_enableTests;
	}
}

/**
 * @package infra
 * @subpackage log
 */
class KalturaStdoutLogger
{
	public function log($message, $priority = KalturaLog::NOTICE)
	{
		echo "[" . date('Y-m-d H:i:s') . "]$message\n";
	}
}

/**
 * @package infra
 * @subpackage log
 */
class KalturaNullLogger
{
		public function log($message, $priority = KalturaLog::NOTICE)
		{
		}
}

/**
 * @package infra
 * @subpackage log
 */
class LogTime 
{
	public function __toString()
	{
		return date('Y-m-d H:i:s');
	}
}

/**
 * @package infra
 * @subpackage log
 */
class LogMethod
{
	private static $_debugBacktraceOptions = null;
	
	public function __toString()
	{
		if(!isset(self::$_debugBacktraceOptions))
			self::$_debugBacktraceOptions = defined('DEBUG_BACKTRACE_IGNORE_ARGS') ? DEBUG_BACKTRACE_IGNORE_ARGS : false;
		
		$backtraceIndex = 3;
		$backtrace = debug_backtrace(self::$_debugBacktraceOptions);
		
		while(
			$backtraceIndex < count($backtrace)
			&&
			(
//				$backtrace[$backtraceIndex]["file"] == __FILE__
//				||
				(isset($backtrace[$backtraceIndex]["class"]) && is_int(strpos($backtrace[$backtraceIndex]["class"], 'Log')))
				||
				(isset($backtrace[$backtraceIndex]["function"]) && $backtrace[$backtraceIndex]["function"] == 'log')
			)
		)
			$backtraceIndex++;
			
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

/**
 * @package infra
 * @subpackage log
 */
class LogDuration
{
	static $_lastMicroTime = null;
	public function __toString()
	{
		$curTime = microtime(true);
		
		if (self::$_lastMicroTime === null)
		{
			if (isset($GLOBALS["start"]))
				self::$_lastMicroTime = $GLOBALS["start"];
			else
				self::$_lastMicroTime = $curTime;
		}
		$result = sprintf("%.6f", $curTime - self::$_lastMicroTime);
			
		self::$_lastMicroTime = $curTime;
		
		return $result;
	}
}

/**
 * @package infra
 * @subpackage log
 */
class SessionIndex
{
	static $_currentIndex = 0;
	public function __toString()
	{
		self::$_currentIndex++;
		return '' . self::$_currentIndex;
	}
}
