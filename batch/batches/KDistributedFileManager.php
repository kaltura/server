<?php
/**
 * 
 * @package Scheduler
 *
 */

class KDistributedFileManager
{
	private $fileCacheTimeout = null;
	private $localRoot = null;
	private $remoteRoot = null;
	
	/**
	 * @param string $localRoot
	 * @param string $remoteRoot
	 * @param int $fileCacheTimeout
	 */
	public function __construct($localRoot, $remoteRoot, $fileCacheTimeout = null)
	{
		$this->localRoot = $localRoot;
		$this->remoteRoot = $remoteRoot;
		$this->fileCacheTimeout = $fileCacheTimeout;
	}
	
	public function getLocalPath($localPath, $remotePath, &$errDescription)
	{
		KalturaLog::info("Translating remote path [$remotePath] to local path [$localPath]");
				
		if(file_exists($localPath))
		{
			if(!$this->fileCacheTimeout)
				return true;
			
			clearstatcache();
			if(filemtime($localPath) > (time() - $this->fileCacheTimeout))
				return true;
				
			@unlink($localPath);
		}
		
		return $this->fetchFile($remotePath, $localPath, $errDescription);
	}
	
	/**
	 * @param string $localPath
	 * @return string
	 */
	public function getRemoteUrl($localPath)
	{
		KalturaLog::debug("str_replace($this->localRoot, $this->remoteRoot, $localPath)");	
		return str_replace($this->localRoot, $this->remoteRoot, $localPath);
	}

	/**
	 * @param string $remotePath
	 * @param string $localPath
	 * @param string $errDescription
	 * @return boolean
	 */
	private function fetchFile($remotePath, $localPath, &$errDescription)
	{
		KalturaLog::debug("Fetch url [$remotePath] to file [$localPath]");
				
		try
		{
			$folder = substr($localPath, 0, strrpos($localPath, '/'));
			if(!file_exists($folder))
				mkdir($folder, 777, true);
			
			$curlWrapper = new KCurlWrapper($remotePath);
			$curlHeaderResponse = $curlWrapper->getHeader(true);
			if(!$curlHeaderResponse || $curlWrapper->getError())
			{
				$errDescription = "Error: " . $curlWrapper->getError();
				return false;
			}
			
			if(!$curlHeaderResponse->isGoodCode())
			{
				$errDescription = "HTTP Error: " . $curlHeaderResponse->code . " " . $curlHeaderResponse->codeName;
				return false;
			}
			$fileSize = null;
			if(isset($curlHeaderResponse->headers['content-length']))
				$fileSize = $curlHeaderResponse->headers['content-length'];
			$curlWrapper->close();
				
			KalturaLog::debug("Executing curl");
			$curlWrapper = new KCurlWrapper($remotePath);
			$res = $curlWrapper->exec($localPath);
			KalturaLog::debug("Curl results: $res");
		
			if(!$res || $curlWrapper->getError())
			{
				$errDescription = "Error: " . $curlWrapper->getError();
				$curlWrapper->close();
				return false;
			}
			$curlWrapper->close();
			
			if(!file_exists($localPath))
			{
				$errDescription = "Error: output file doesn't exist";
				return false;
			}
				
			if($fileSize)
			{
				clearstatcache();
				if(filesize($localPath) != $fileSize)
				{
					$errDescription = "Error: output file have a wrong size";
					return false;
				}
			}
		}
		catch(Exception $ex)
		{
			$errDescription = "Error: " . $ex->getMessage();
			return false;
		}
		
		return true;
	}
}