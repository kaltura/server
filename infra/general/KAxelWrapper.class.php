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
		
		if (!$this->checkAxelInstalled())
		{
			KalturaLog::debug("Axel is not installed");
		}
	}
	
	public function getErrorNumber()
	{
		return isset($this->errorNumber) ? $this->errorNumber : 0;
	}
	
	public function getErrorMsg()
	{
		return isset($this->error) ? $this->error : false;
	}
	
	private function checkAxelInstalled()
	{
		return $this->execCmd("$this->axelPath --version");
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
		
		if ($resultCode)
		{
			KalturaLog::debug("Executed command result code [$resultCode]");
			$this->errorNumber = $resultCode;
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
		
		if (class_exists('KalturaMonitorClient'))
		{
			KalturaMonitorClient::monitorAxel($this->host, $end - $start);
		}
		
		$this->httpCode = $this->getHttpCodeFromLog();
		$this->errorNumber = $this->getErrorNumber();
		$this->error = $this->getErrorMsgFromLog();
		
		self::$partiallyDownloaded = $this->isPartialDownload();
		self::$fileSize = $this->getFileSizeInBytesFromLogFile();
		
		return $this->downloadCompleted($result);
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
		if (kFile::checkFileExists($this->getLogPath()))
		{
			$msg = "Deleting log file at [$this->logPath] - ";
			$msg .= kFile::unlink($this->logPath) ? 'success' : 'failed';
			KalturaLog::debug($msg);
		}
		
		if (kFile::checkFileExists($this->getLogPathErr()))
		{
			$msg = "Deleting log file at [$this->logPathErr] - ";
			$msg .= kFile::unlink($this->logPathErr) ? 'success' : 'failed';
			KalturaLog::debug($msg);
		}
	}
	
	/**
	 * @param $logFilePath
	 * @param int $lengthInBytes if negative number will read from end of file
	 * @return false|string
	 */
	private function getLogFileContentIfExists($logFilePath, $lengthInBytes = 3000)
	{
		if (!$logFilePath || !kFile::checkFileExists($logFilePath))
		{
			KalturaLog::debug("Axel log path is not set or does not exist");
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
		
		if (!preg_match('/File size:.*bytes/', $logFileContent, $matches))
		{
			KalturaLog::debug("Failed to extract 'File size' line from log path at [$this->logPath]");
			return false;
		}
		
		$fileSizeLine = $matches[0];
		
		if (!preg_match('/[0-9]+\s*bytes/', $fileSizeLine, $matches))
		{
			KalturaLog::debug("Failed to extract 'File size in bytes' value from line [$fileSizeLine]");
			return false;
		}
		
		$fileSizeBytesPostfix = $matches[0];
		
		if (!preg_match('/[0-9]+/', $fileSizeBytesPostfix, $matches))
		{
			KalturaLog::debug("Failed to extract 'File size' value from line [$fileSizeBytesPostfix]");
			return false;
		}
		
		return $matches[0];
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
		return preg_match('/Downloaded/', $logFileContent);
	}
	
	private function getHttpCodeFromLog()
	{
		$logFileContent = $this->getLogFileContentIfExists($this->getLogPath());
		if (!$logFileContent)
		{
			return 0;
		}
		
		if (preg_match('/Starting download/', $logFileContent))
		{
			return KCurlHeaderResponse::HTTP_STATUS_OK;
		}
		
		$logFileContent = $this->getLogFileContentIfExists($this->getLogPathErr());
		if (!$logFileContent)
		{
			return 0;
		}
		
		if (preg_match('/ERROR.*/', $logFileContent, $matches))
		{
			$httpCodeLine = $matches[0];
			if (preg_match('/[0-9]+/', $httpCodeLine, $matches))
			{
				return trim($matches[0]);
			}
			
			KalturaLog::debug("Could not extract http code from line [$httpCodeLine]");
			return 0;
		}
		
		KalturaLog::debug("Could not extract http status code from log file at [$this->logPath]");
		return 0;
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
		$sourceUrl = trim($sourceUrl);
		if (strpos($sourceUrl, '://') === false && substr($sourceUrl, 0, 1) != '/')
		{
			$sourceUrl = 'http://' . $sourceUrl;
		}
		
		//Replace # sign to avoid cases where it's part of the user/password. The # sign is considered as fragment part of the URL.
		//https://bugs.php.net/bug.php?id=73754
		$sourceUrl = preg_replace("/#/", "_kHash_", $sourceUrl, -1, $replaceCount);
		
		// extract information from URL and job data
		$parts = parse_url($sourceUrl);
		if($replaceCount)
		{
			$parts = preg_replace("/_kHash_/", "#", $parts);
		}
		
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
		$sourceUrl = trim($url);
		if (strpos($sourceUrl, '://') === false && substr($sourceUrl, 0, 1) != '/')
		{
			$sourceUrl = 'http://' . $sourceUrl;
		}
		
		//Replace # sign to avoid cases where it's part of the user/password. The # sign is considered as fragment part of the URL.
		//https://bugs.php.net/bug.php?id=73754
		$sourceUrl = preg_replace("/#/", "_kHash_", $sourceUrl, -1, $replaceCount);
		
		// extract information from URL and job data
		$parts = parse_url($sourceUrl);
		
		if (isset($parts['user']) || isset($parts['pass']))
		{
			return true;
		}
		
		return false;
	}
}
