<?php

class KAxelWrapper extends KCurlWrapper
{
	const DEFAULT_CONCURRENT_CONNECTIONS = 10;
	
	/**
	 * @var null|bool
	 */
	public static $partiallyDownloaded = null;
	
	/**
	 * @var false|string
	 */
	public static $fileSize = null;
	
	/**
	 * @var int|mixed
	 */
	public $concurrentConnections;
	
	/**
	 * @var mixed|null
	 */
	public $axelPath;
	
	/**
	 * @var string
	 */
	private $url;
	
	/**
	 * @var string
	 */
	private $destFile;
	
	/**
	 * @var string|null
	 */
	private $logPath = null;
	
	/**
	 * @var string|null
	 */
	private $logPathErr = null;
	
	public function __construct($params = null)
	{
		parent::__construct($params);
		$this->concurrentConnections = isset($params->concurrentConnections) ? $params->concurrentConnections : self::DEFAULT_CONCURRENT_CONNECTIONS;
		$this->axelPath = isset($params->axelPath) ? $params->axelPath : null;
	}
	
	public function getErrorMsg()
	{
		return isset($this->error) ? $this->error : false;
	}
	
	private function setInternalUrlErrorResults($url)
	{
		$this->errorNumber = -1;
		$this->error = "Internal not allowed url [$url] - axel will not be invoked";
	}
	
	private function setLogPath()
	{
		$this->logPath = $this->destFile . '.log';
	}
	
	private function getLogPath()
	{
		return $this->logPath;
	}
	
	private function setLogPathErr()
	{
		$this->logPathErr = $this->destFile . '.err.log';
	}
	
	private function getLogPathErr()
	{
		return $this->logPathErr;
	}
	
	/**
	 * @param $cmdLine
	 * @param false $returnValue
	 * @return bool|array
	 */
	private function execCmd($cmdLine, $returnValue = false)
	{
		KalturaLog::debug("Executing command: [$cmdLine]");
		
		unset($output);
		exec($cmdLine, $output, $resultCode);
		
		$this->errorNumber = $resultCode;
		
		if ($resultCode)
		{
			KalturaLog::debug("Executed command result code [$resultCode]");
			return false;
		}
		
		return $returnValue ? $output : true;
	}
	
	/**
	 * @param $sourceUrl
	 * @param null $destFile
	 * @param null $progressCallBack
	 * @param bool $allowInternalUrl
	 * @return bool
	 */
	public function exec($sourceUrl, $destFile = null, $progressCallBack = null, $allowInternalUrl = false)
	{
		if (is_null($destFile))
		{
			KalturaLog::debug("Destination file cannot be 'null' - aborting");
			return false;
		}
		
		$this->destFile = $destFile;
		$this->url = self::getSourceUrl($sourceUrl, $this->protocol, $this->host, $allowInternalUrl);
		if (!$this->url)
		{
			$this->setInternalUrlErrorResults($sourceUrl);
			return false;
		}
		
		$this->setLogPath();
		$this->setLogPathErr();
		
		return $this->execAxel();
	}
	
	private function execAxel()
	{
		$start = microtime(true);
		$result = $this->execCmd("$this->axelPath --max-redirect=0 -n $this->concurrentConnections -o $this->destFile $this->url > $this->logPath 2> $this->logPathErr");
		$end = microtime(true);
		
		$this->httpCode = $this->getHttpCodeFromLog();
		$this->error = $this->getErrorMsgFromLog();
		
		self::$partiallyDownloaded = $this->isPartialDownload();
		self::$fileSize = $this->getFileSizeInBytesFromLogFile();
		
		if (class_exists('KalturaMonitorClient'))
		{
			KalturaMonitorClient::monitorAxel($this->host, $end - $start, $this->getErrorCode());
		}
		
		return $this->downloadCompleted($result);
	}
	
	private function getErrorCode()
	{
		if ($this->httpCode && ($this->httpCode < 200 || $this->httpCode > 300))
		{
			return 'HTTP_' . $this->httpCode;
		}
		else if ($this->error)
		{
			KalturaLog::debug("Axel Error: [$this->error]");
			return 'ERROR';
		}
		else if ($this->errorNumber)
		{
			return 'AXEL_' . $this->errorNumber;
		}
		
		return null;
	}
	
	/**
	 * @return void
	 */
	public function close()
	{
		parent::close();
		$this->deleteLogFiles();
	}
	
	private function deleteLogFiles()
	{
		$logPathArray = array(
			$this->getLogPath(),
			$this->getLogPathErr()
		);
		
		foreach ($logPathArray as $logPath)
		{
			if (kFile::checkFileExists($logPath))
			{
				$msg = "Deleting log file at [$logPath] - ";
				$msg .= kFile::unlink($logPath) ? 'success' : 'failed';
				KalturaLog::debug($msg);
			}
		}
	}
	
	/**
	 * @param $logFilePath
	 * @param int $lengthInBytes if negative number will read from end of file
	 * @return false|string
	 */
	private function getLogFileContentIfExists($logFilePath, $lengthInBytes = 3000)
	{
		if (!$logFilePath || !kFile::fileSize($logFilePath))
		{
			KalturaLog::debug("Axel log path is not set or is empty log file");
			return false;
		}
		
		if ($lengthInBytes < 0)
		{
			$fromByte = max(kFile::fileSize($logFilePath) + $lengthInBytes, 0);
			$toByte = -1;
		}
		
		$fromByte = isset($fromByte) ? $fromByte : 0;
		$toByte = isset($toByte) ? $toByte : min(kFile::fileSize($logFilePath), $lengthInBytes);
		
		$logFileContent = kFile::getFileContent($logFilePath, $fromByte, $toByte);
		if (!$logFileContent)
		{
			KalturaLog::debug("Failed to get file content from path [$logFilePath]");
			return false;
		}
		
		return $logFileContent;
	}
	
	private function getFileSizeInBytesFromLogFile()
	{
		$logFileContent = $this->getLogFileContentIfExists($this->getLogPath());
		if (!$logFileContent)
		{
			return false;
		}
		
		if (!preg_match('/File size:.*?([0-9]+) bytes/', $logFileContent, $matches) || !isset($matches[1]))
		{
			KalturaLog::debug('Failed to extract "File size" value from log');
		}
		
		return $matches[1];
	}
	
	private function getFileDownloadPercentageFromLogFile()
	{
		$logFileContent = $this->getLogFileContentIfExists($this->getLogPath(), -500);
		if (!$logFileContent)
		{
			return false;
		}
		
		if (!preg_match_all('/\[\s*[0-9]{1,3}%]/', $logFileContent, $matches))
		{
			KalturaLog::debug("Could not extract downloaded percentage from last log lines [$logFileContent]");
			return false;
		}
		
		$downloadedPercentage = ltrim(trim(end($matches[0]), '[]'));
		return rtrim($downloadedPercentage, '%');
	}
	
	private function isPartialDownload()
	{
		$percentage = $this->getFileDownloadPercentageFromLogFile();
		KalturaLog::debug("Downloaded content percentage = [$percentage%]");
		return $percentage !== false && $percentage != 100;
	}
	
	private function downloadCompleted($result)
	{
		if (!$result || self::$partiallyDownloaded)
		{
			return false;
		}
		
		$logFileContent = $this->getLogFileContentIfExists($this->getLogPath(), -150);
		return strpos($logFileContent, 'Downloaded') !== false;
	}
	
	private function getHttpCodeFromLog()
	{
		$logFileContent = $this->getLogFileContentIfExists($this->getLogPath());
		if (!$logFileContent)
		{
			return 0;
		}
		
		if (strpos($logFileContent, 'Starting download') !== false)
		{
			return KCurlHeaderResponse::HTTP_STATUS_OK;
		}
		
		$logFileContent = $this->getLogFileContentIfExists($this->getLogPathErr());
		if (!$logFileContent)
		{
			return 0;
		}
		
		if (!preg_match('/ERROR ([0-9]+)/', $logFileContent, $matches) || !isset($matches[1]))
		{
			KalturaLog::debug("Could not extract http status code from log file");
			return 0;
		}
		
		return $matches[1];
	}
	
	private function getErrorMsgFromLog()
	{
		if (!kFile::fileSize($this->getLogPathErr()))
		{
			return false;
		}
		
		$logFileContent = $this->getLogFileContentIfExists($this->getLogPathErr());
		return explode("\n", $logFileContent)[0];
	}
	
	/**
	 * @param $sourceUrl
	 * @param $protocol
	 * @param $host
	 * @param false $allowInternalUrl
	 * @return false|string
	 */
	public static function getSourceUrl($sourceUrl, &$protocol, &$host, $allowInternalUrl = false)
	{
		$parts = KAxelWrapper::parseUrl($sourceUrl);
		
		if (!isset($parts['scheme']) || !isset($parts['host']))
		{
			KalturaLog::log("Failed to parse url [$sourceUrl]");
			return false;
		}
		
		$host = $parts['host'];
		
		if (!$allowInternalUrl && self::isInternalHost($parts['host']) && !self::isWhiteListedInternalUrl($sourceUrl))
		{
			KalturaLog::log("Url [$sourceUrl] is internal and not whiteListed");
			return false;
		}
		
		if (in_array($parts['scheme'], array('ftp', 'ftps')))
		{
			$protocol = self::HTTP_PROTOCOL_FTP;
		}
		else
		{
			$protocol = self::HTTP_PROTOCOL_HTTP;
		}
		
		$url = $parts['scheme'] . '://' . $parts['host'];
		
		if (isset($parts['port']))
		{
			$url .= ':' . $parts['port'];
		}
		
		if (isset($parts['path']))
		{
			$url .= $parts['path'];
		}
		
		if (isset($parts['query']))
		{
			$url .= '?' . $parts['query'];
		}
		
		$url = self::encodeUrl($url);
		
		if ($sourceUrl != $url)
		{
			KalturaLog::info("Input url [$sourceUrl] final url [$url]");
		}
		else
		{
			KalturaLog::info("Input url [$url]");
		}
		return $url;
	}
	
	public static function checkUserAndPassOnUrl($url)
	{
		$parts = KAxelWrapper::parseUrl($url);
		
		if (isset($parts['user']) || isset($parts['pass']))
		{
			return true;
		}
		
		return false;
	}
	
	public static function parseUrl(&$url)
	{
		$url = trim($url);
		if (strpos($url, '://') === false && substr($url, 0, 1) != '/')
		{
			$url = 'http://' . $url;
		}
		
		//Replace # sign to avoid cases where it's part of the user/password. The # sign is considered as fragment part of the URL.
		//https://bugs.php.net/bug.php?id=73754
		$url = preg_replace("/#/", "_kHash_", $url, -1, $replaceCount);
		
		// extract information from URL and job data
		$parts = parse_url($url);
		
		if($replaceCount)
		{
			$parts = preg_replace("/_kHash_/", "#", $parts);
		}
		
		return $parts;
	}
	
	public static function checkAxelInstalled($axelPath = null)
	{
		kExecWrapper::exec("$axelPath --version", $output, $returnValue);
		return !$returnValue;
	}
}
