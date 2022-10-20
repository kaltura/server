<?php

use Axel\AxelDownload;

require_once (__DIR__ . '/../../vendor/axel/axel-autoloader.php');

class KAxelWrapper extends KCurlWrapper
{
	const CONCURRENT_CONNECTIONS = 10;
	
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
	private $logDirPath;
	
	/**
	 * @var string|null
	 */
	private $logPath;
	
	public function __construct($params = null)
	{
		parent::__construct($params);
		$this->concurrentConnections = isset($params->concurrentConnections) ? $params->concurrentConnections : self::CONCURRENT_CONNECTIONS;
		$this->axelPath = isset($params->axelPath) ? $params->axelPath : null;
		
		$this->axel = new AxelDownload($this->axelPath, $this->concurrentConnections);
		
		if (!$this->axel->checkAxelInstalled())
		{
			KalturaLog::debug('Axel is not installed - aborting');
			return false;
		}
		
		$this->logDirPath = isset($params->logDirPath) ? rtrim($params->logDirPath, '/') : null;
		
		return true;
	}
	
	/**
	 *
	 * @return string The last error encountered
	 */
	public function getErrorMsg()
	{
		return $this->axel->error;
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
		KalturaLog::debug("Starting Axel Download");
		$this->axel->start($this->url, $this->destFile);
		$end = microtime(true);

		if (class_exists('KalturaMonitorClient'))
		{
			KalturaMonitorClient::monitorCurl($this->host, $end - $start, $this->ch);
		}
		
		$this->httpCode = $this->getInfo(CURLINFO_HTTP_CODE);
		$this->errorNumber = curl_errno($this->ch);
		$this->error = $this->getErrorMsg();
		
		return ($this->axel->last_command === AxelDownload::COMPLETED);
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
		
		// consider adding conf for 'params.deleteLogFile' so we can decide if to delete or not (for debugging maybe)
		$msg = "Deleting log file at [$this->logPath] - ";
		$msg .= $this->axel->clearCompleted() ? 'success' : 'failed';
		KalturaLog::debug($msg);
	}
	
	private function setLogPath()
	{
		$this->logPath = $this->logDirPath ? $this->logDirPath . basename($this->destFile) : $this->destFile;
		$this->logPath .= '_' . time() . '.log';
		$this->axel->log_path = $this->logPath;
	}
}
