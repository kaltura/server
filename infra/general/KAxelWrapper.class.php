<?php

use Axel\AxelDownload;

require_once (__DIR__ . '/../../vendor/axel/axel-autoloader.php');

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
	 * @var AxelDownload
	 */
	public $axel;
	
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
	private $logPath;
	
	public function __construct($params = null)
	{
		parent::__construct($params);
		$this->concurrentConnections = isset($params->concurrentConnections) ? $params->concurrentConnections : self::DEFAULT_CONCURRENT_CONNECTIONS;
		$this->axelPath = isset($params->axelPath) ? $params->axelPath : null;
		
		$this->axel = new AxelDownload($this->axelPath, $this->concurrentConnections);
		
		if (!$this->axel->checkAxelInstalled())
		{
			$processError = $this->axel->error;
			KalturaLog::debug("Axel is not installed - process error [$processError]");
			return false;
		}
		
		return true;
	}
	
	public function getErrorNumber()
	{
		if (isset($this->axel->processExitCode))
		{
			return $this->axel->processExitCode;
		}
		
		if (isset($this->error))
		{
			return $this->errorNumber;
		}
		
		return 0;
	}

	public function getErrorMsg()
	{
		if (isset($this->axel->error))
		{
			return $this->axel->error;
		}
		
		if (isset($this->error))
		{
			return $this->error;
		}
		
		return false;
	}
	
	private function setInternalUrlErrorResults($url)
	{
		$this->errorNumber = -1;
		$this->error = "Internal not allowed url [$url] - axel will not be invoked";
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
			KalturaLog::debug("Axel destination file cannot be 'null' - aborting");
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
		
		return $this->execAxel();
	}
	
	private function execAxel()
	{
		$start = microtime(true);
		$this->axel->start($this->url, $this->destFile);
		$end = microtime(true);

		if (class_exists('KalturaMonitorClient'))
		{
			KalturaMonitorClient::monitorAxel($this->host, $end - $start);
		}
		
		$this->httpCode = $this->getHttpCodeFromLog();
		$this->errorNumber = $this->getErrorNumber();
		$this->error = $this->getErrorMsg();
		
		$completed = $this->axel->last_command === AxelDownload::COMPLETED;
		
		if (!$completed || $this->errorNumber)
		{
			self::$partiallyDownloaded = $this->isPartialDownload();
			self::$fileSize = $this->getFileSizeInBytesFromLogFile();
		}
		
		return $completed;
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
		
		$url = $parts['scheme'] . '://';
		
		$userPwd = '';
		if (isset($parts['user']) && isset($parts['pass']))
		{
			$userPwd = $parts['user'] . ':' . $parts['pass'] . '@';
			$url .= $userPwd;
		}
		
		$url .= $parts['host'];
		
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
			KalturaLog::info("Input url [$sourceUrl] final url [$url] userpwd [$userPwd]");
		}
		else
		{
			KalturaLog::info("Input url [$url]");
		}
		return $url;
	}
	
	/**
	 * @return void
	 */
	public function close()
	{
		parent::close();
		
		if (kFile::checkFileExists($this->logPath))
		{
			$msg = "Deleting log file at [$this->logPath] - ";
			$msg .= kFile::unlink($this->logPath) ? 'success' : 'failed';
			KalturaLog::debug($msg);
		}
	}
	
	private function setLogPath()
	{
		$this->logPath = $this->destFile . '.log';
		$this->axel->log_path = $this->logPath;
	}
	
	private function getLogPath()
	{
		return isset($this->logPath) && is_string($this->logPath) ? $this->logPath : false;
	}
	
	private function getLogFileContentIfExists()
	{
		if (!$this->getLogPath() || !kFile::checkFileExists($this->getLogPath()))
		{
			KalturaLog::debug("Axel log path is not set or does not exist");
			return false;
		}
		
		$logFileContent = kFile::getFileContent($this->getLogPath());
		if (!$logFileContent)
		{
			KalturaLog::debug("Failed to get file content from path [$this->logPath]");
			return false;
		}
		
		return $logFileContent;
	}
	
	private function getFileSizeInBytesFromLogFile()
	{
		$logFileContent = $this->getLogFileContentIfExists();
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
		
		return $matches;
	}
	
	private function getFileDownloadPercentageFromLogFile()
	{
		$logFileContent = $this->getLogFileContentIfExists();
		if (!$logFileContent)
		{
			return false;
		}
		
		$lastLogLines = substr($logFileContent, -150);
		if (!$lastLogLines)
		{
			KalturaLog::debug("Could not get last log lines from log file at [$this->logPath]");
			return false;
		}
		
		if (!preg_match('/\[\s*[0-9]{1,3}%]/', $lastLogLines, $matches))
		{
			KalturaLog::debug("Could not extract downloaded percentage from last log lines [$lastLogLines]");
			return false;
		}
		
		$downloadedPercentage = trim(trim($matches[0], '[]'));
		return rtrim($downloadedPercentage, '%');
	}
	
	private function isPartialDownload()
	{
		$percentage = $this->getFileDownloadPercentageFromLogFile();
		KalturaLog::debug("Downloaded content percentage = [$percentage%]");
		return $percentage !== false && $percentage != 100;
	}
	
	private function getHttpCodeFromLog()
	{
		$logFileContent = $this->getLogFileContentIfExists();
		if (!$logFileContent)
		{
			return false;
		}
		
		if (preg_match('/Starting download/', $logFileContent))
		{
			return KCurlHeaderResponse::HTTP_STATUS_OK;
		}
		
		if (preg_match('/HTTP.*/', $logFileContent, $matches))
		{
			$httpCodeLine = $matches[0];
			if (!preg_match('/\s[0-9]*\s/', $httpCodeLine, $matches))
			{
				KalturaLog::debug("Could not extract http code from line [$httpCodeLine]");
				return false;
			}
			
			return trim($matches[0]);
		}
		
		KalturaLog::debug("Could not extract http status code from log file at [$this->logPath]");
		return 0;
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
