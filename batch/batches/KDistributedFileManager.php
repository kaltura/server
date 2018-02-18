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
	
	public function getLocalPath($localPath, $remotePath, &$errDescription, &$fetched = false)
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
		$fetched = true;
		$res = $this->fetchFile($remotePath, $localPath, $errDescription);
		if(!$res) {
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				KalturaLog::warning("Going to flush DNS: ");
				$output = system( "ipconfig /flushdns" , $rc);
				KalturaLog::warning($output);
			}
		}
		
		return $res;
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
		try
		{
			$folder = substr($localPath, 0, strrpos($localPath, '/'));
			if(!file_exists($folder))
				mkdir($folder, 777, true);
			
			$curlWrapper = new KCurlWrapper();
			$curlHeaderResponse = $curlWrapper->getHeader($remotePath, true);
			$removeServer = isset($curlHeaderResponse->headers['X-Me']) ? $curlHeaderResponse->headers['X-Me'] : "unknown";
			
			if(!$curlHeaderResponse || $curlWrapper->getError())
			{
				$errDescription = "Error: ($removeServer) " . $curlWrapper->getError();
				return false;
			}
			
			if(!$curlHeaderResponse->isGoodCode())
			{
				$errDescription = "HTTP Error: ($removeServer) " . $curlHeaderResponse->code . " " . $curlHeaderResponse->codeName;
				return false;
			}
			$fileSize = null;
			if(isset($curlHeaderResponse->headers['content-length']))
				$fileSize = $curlHeaderResponse->headers['content-length'];
			$curlWrapper->close();
				
			// overcome a 32bit issue with curl fetching >=4gb files
			if (intval("9223372036854775807") == 2147483647 && $fileSize >= 4 * 1024 * 1024 * 1024)
			{
				unlink($localPath);
        		$cmd = "curl -s $remotePath -o $localPath";
				KalturaLog::debug($cmd);
        		exec($cmd);
			}
			else
			{			
				$curlWrapper = new KCurlWrapper();
				$res = $curlWrapper->exec($remotePath, $localPath);
				KalturaLog::debug("Curl results: $res");
			
				if(!$res || $curlWrapper->getError())
				{
					$errDescription = "Error: ($removeServer) " . $curlWrapper->getError();
					$curlWrapper->close();
					return false;
				}
				$curlWrapper->close();
			}
			
			if(!file_exists($localPath))
			{
				$errDescription = "Error: output file doesn't exist";
				return false;
			}
				
			if($fileSize)
			{
				clearstatcache();
				if(kFile::fileSize($localPath) != $fileSize)
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