<?php

require_once(dirname(__file__) . '/../request/infraRequestUtils.class.php');
require_once(dirname(__file__) . '/kRendererBase.php');

/*
 * @package server-infra
 * @subpackage renderers
 */
class kRendererDumpFile implements kRendererBase
{
	const CACHE_FILE_CONTENTS_MAX_SIZE = 262144;	// 256K
	
	protected $filePath;
	protected $fileExt;
	protected $fileSize;
	protected $fileData;
	protected $mimeType;
	protected $maxAge;
	protected $xSendFileAllowed;
	protected $lastModified;
	
	public $partnerId;

	public function __construct($filePath, $mimeType, $xSendFileAllowed, $maxAge = 8640000, $limitFileSize = 0, $lastModified = null)
	{
		$this->filePath = $filePath;
		$this->mimeType = $mimeType;
		$this->maxAge = $maxAge;
		$this->lastModified = $lastModified;
		
		$this->fileExt = pathinfo($filePath, PATHINFO_EXTENSION);
		if ($limitFileSize)
		{
			$this->fileSize = $limitFileSize;
			$this->xSendFileAllowed = false;
		}
		else
		{
			clearstatcache();
			$this->fileSize = kFile::fileSize($filePath);
			$this->xSendFileAllowed = $xSendFileAllowed;
		}
		
		if ($this->fileSize && $this->fileSize < self::CACHE_FILE_CONTENTS_MAX_SIZE)
		{
			 $this->fileData = file_get_contents($this->filePath, false , null , -1, $limitFileSize);
		}
	}
	
	public function validate()
	{
		return $this->fileData || file_exists($this->filePath);
	}
	
	public function output()
	{
		if ($this->maxAge && 
			isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) &&
			$_SERVER['HTTP_IF_MODIFIED_SINCE'] == infraRequestUtils::formatHttpTime($this->lastModified))
		{
			infraRequestUtils::sendCachingHeaders($this->maxAge, false, $this->lastModified);
			header("HTTP/1.1 304 Not Modified");
			return;
		}
		
		$useXsendFile = false;
		$rangeLength = null;
		if (!$this->fileData && $this->xSendFileAllowed && in_array('mod_xsendfile', apache_get_modules()))
			$useXsendFile = true;
		else
			list($rangeFrom, $rangeTo, $rangeLength) = infraRequestUtils::handleRangeRequest($this->fileSize);

		if (class_exists('KalturaMonitorClient'))
		{
			KalturaMonitorClient::monitorDumpFile($this->fileSize, $this->filePath);
		}
				
		infraRequestUtils::sendCdnHeaders($this->fileExt, $rangeLength, $this->maxAge, $this->mimeType, false, $this->lastModified);

		// return "Accept-Ranges: bytes" header. Firefox looks for it when playing ogg video files
		// upon detecting this header it cancels its original request and starts sending byte range requests
		header("Accept-Ranges: bytes");
		header("Access-Control-Allow-Origin:*");		

		if ($this->fileData)
		{
			echo substr($this->fileData, $rangeFrom, $rangeLength);
		}
		else if ($useXsendFile)
		{
			header('X-Kaltura-Sendfile:');
			header("X-Sendfile: {$this->filePath}");
		}
		else
		{
			infraRequestUtils::dumpFilePart($this->filePath, $rangeFrom, $rangeLength);
		}
	}
}
