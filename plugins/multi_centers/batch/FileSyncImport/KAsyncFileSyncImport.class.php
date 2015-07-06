<?php
/**
 *
 *
 * @package plugins.multiCenters
 * @subpackage Scheduler.FileSyncImport
 */
class KAsyncFileSyncImport extends KPeriodicWorker
{
	protected $curlWrapper;

	public function run($jobs = null)
	{
		// get worker parameters
		$filter = $this->getFilter();
		$sourceDc = $this->getAdditionalParams("sourceDc");
		$maxCount = $this->getAdditionalParams("maxCount");
		$maxSize = $this->getAdditionalParams("maxSize");
		
		$this->curlWrapper = new KCurlWrapper(self::$taskConfig->params);
		
		$multiCentersPlugin = KalturaMultiCentersClientPlugin::get(self::$kClient);
		
		for (;;)
		{
			// lock file syncs to import
			$lockResult = $multiCentersPlugin->filesyncImportBatch->lockPendingFileSyncs(
					$filter, 
					$this->getId(), 
					$sourceDc,
					$maxCount,
					$maxSize);

			// handle all dirs and empty files first
			$fileSyncs = array();
			foreach ($lockResult->fileSyncs as $fileSync)
			{				
				if ($fileSync->isDir)
				{
					$sourceUrl = self::getSourceUrl($fileSync->originalId, $lockResult->baseUrl, $lockResult->dcSecret);
					if ($this->fetchDir($sourceUrl, self::getFullPath($fileSync)))
					{
						$this->markFileSyncAsReady($fileSync);
					}
				}
				else if ($fileSync->fileSize == 0)
				{
					if ($this->fetchEmptyFile(self::getFullPath($fileSync)))
					{
						$this->markFileSyncAsReady($fileSync);
					}
				}
				else 
				{
					$fileSyncs[] = $fileSync;
				}
			}

			// handle regular files
			if (count($fileSyncs) == 1)
			{
				$fileSync = reset($fileSyncs);
				$sourceUrl = self::getSourceUrl($fileSync->originalId, $lockResult->baseUrl, $lockResult->dcSecret);
				if ($this->fetchFile($sourceUrl, self::getFullPath($fileSync), $fileSync->fileSize))
				{
					$this->markFileSyncAsReady($fileSync);
				}
			}
			else if (count($fileSyncs) > 1)
			{
				$this->fetchMultiFiles($fileSyncs, $lockResult->baseUrl, $lockResult->dcSecret);
			}
			
			// if the limit was not reached, wait for more file syncs to become available
			if (!$lockResult->limitReached)
			{
				sleep(1);
			}
		}
		
		$this->curlWrapper->close();
	}
	
	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::FILESYNC_IMPORT;
	}

	protected function getFilter()
	{
		if(KBatchBase::$taskConfig->filter)
		{
			return KBatchBase::$taskConfig->filter;
		}
		return new KalturaFileSyncFilter();
	}
	
	static protected function getSourceUrl($fileSyncId, $baseUrl, $dcSecret)
	{
		$fileHash = md5($dcSecret . $fileSyncId);
		return $baseUrl . "/index.php/extwidget/servefile/id/$fileSyncId/hash/$fileHash";
	}
	
	static protected function getMultiSourceUrl($fileSyncIds, $baseUrl, $dcSecret)
	{
		$fileHash = md5($dcSecret . $fileSyncIds);
		return $baseUrl . "/index.php/extwidget/serveMultiFile/ids/$fileSyncIds/hash/$fileHash";
	}
	
	static protected function getFullPath(KalturaFileSync $fileSync)
	{
		return $fileSync->fileRoot . $fileSync->filePath;
	}
	
	protected function markFileSyncAsReady(KalturaFileSync $fileSync)
	{
		$updateFileSync = new KalturaFileSync;
		$updateFileSync->status = KalturaFileSyncStatus::READY;
		$updateFileSync->fileRoot = $fileSync->fileRoot;
		$updateFileSync->filePath = $fileSync->filePath;
	
		try
		{
			$fileSyncPlugin = KalturaFilesyncClientPlugin::get(self::$kClient);
			$fileSyncPlugin->fileSync->update($fileSync->id, $updateFileSync);
		}
		catch(KalturaException $e)
		{
			KalturaLog::err($e);
		}
		catch(KalturaClientException $e)
		{
			KalturaLog::err($e);
		}
	}
	
	private function fetchEmptyFile($destination) {
		
		$res = self::createAndSetDir(dirname($destination));
		if ( !$res )
		{
			KalturaLog::err('Cannot create destination directory ['.dirname($destination).']');
			return false;
		}
		
		$res = touch($destination);
		if ( !$res )
		{
			KalturaLog::err("Cannot create file [$destination]");
			return false;
		}
		return true;
	}

	/**
	 * Fetch all content of a $sourceUrl that leads to a directory and save it in the given $dirDestination.
	 * @param string $sourceUrl
	 * @param string $dirDestination
	 */
	private function fetchDir($sourceUrl, $dirDestination)
	{
		KalturaLog::debug('fetchDir - source url ['.$sourceUrl.'], destination ['.$dirDestination.']');
		
		// create directory if does not exist
		$res = $this->createAndSetDir($dirDestination);
		if (!$res) 
		{
			KalturaLog::err("Cannot create destination directory [$dirDestination]");
			return false;
		}
		
		// get directory contents
		KalturaLog::debug('Executing CURL to get directory contents for ['.$sourceUrl.']');	
		$contents = $this->curlWrapper->exec($sourceUrl);
		$curlError = $this->curlWrapper->getError();
		$curlErrorNumber = $this->curlWrapper->getErrorNumber();
		
		if ($contents === false || $curlError) 
		{
			KalturaLog::err("$curlError");
			return false;
		}
		$contents = unserialize($contents); // if an exception is thrown, it will be catched in fetchUrl
		
		// sort contents alphabetically - this is important so that we will first encounter directories and only later the files in them
		sort($contents, SORT_STRING);		
		
		// fetch each direcotry content
		foreach ($contents as $current)
		{
			$name     = trim($current[0],' /');
			$type     = trim($current[1]);
			$filesize = trim($current[2]);
			
			$newUrl = $sourceUrl .'/fileName/'.base64_encode($name);
			
			if (!$type || !$filesize)
			{
				$curlHeaderResponse = $this->fetchHeader($newUrl);
				if (!$curlHeaderResponse) 
				{
					return false;
				}
				// check if current is a file or directory
				$isDir = $this->isDirectoryHeader($curlHeaderResponse);
				$filesize = $this->getFilesizeFromHeader($curlHeaderResponse);
			}
			else 
			{
				$isDir = $type === 'dir';
			}
			
			if ($isDir)
			{
				// is a directory - no need to fetch from server, just create it and proceed
				$res = $this->createAndSetDir($dirDestination.'/'.$name);
				if (!$res)
				{
					KalturaLog::err('Cannot create destination directory ['.$dirDestination.'/'.$name.']');
					return false;
				}
			}
			else
			{
				// is a file - fetch it from server
				$res = $this->fetchFile($newUrl, $dirDestination.'/'.$name, $filesize);
				if (!$res) 
				{
					return false;
				}
			}
		}

		KalturaLog::debug('fetchDir - completed successfully');
		return true;
	}
	
	/**
	 * Download a file from $sourceUrl to $fileDestination
	 * @param string $sourceUrl
	 * @param string $fileDestination
	 * @param KCurlHeaderResponse $curlHeaderResponse header fetched for the $sourceUrl
	 */
	private function fetchFile($sourceUrl, $fileDestination, $fileSize = null)
	{
		KalturaLog::debug('fetchFile - source url ['.$sourceUrl.'], destination ['.$fileDestination.']');
		
		if (!$fileSize)
		{
			// fetch header if not given
			$curlHeaderResponse = $this->fetchHeader($sourceUrl);
			if (!$curlHeaderResponse) 
			{
				return false;
			}
			
			// try to get file size from headers
			$fileSize = $this->getFilesizeFromHeader($curlHeaderResponse);
		}		

		// if file already exists - check if we can start from specific offset on exising partial content
		$resumeOffset = 0;
		if($fileSize && file_exists($fileDestination))
		{
			clearstatcache();
			$actualFileSize = kFile::fileSize($fileDestination);
			if($actualFileSize >= $fileSize)
			{
				// file download finished ?
				KalturaLog::debug('File exists with size ['.$actualFileSize.'] - checking if finished...');
				return $this->checkFile($fileDestination, $fileSize);
			}
			else
			{
				// will resume from the current offset
				KalturaLog::debug('File partialy exists - resume offset set to ['.$actualFileSize.']');
				$resumeOffset = $actualFileSize;
			}
		}
		
		for (;;)
		{
			// get http body
			if($resumeOffset)
			{
				// will resume from the current offset
				$this->curlWrapper->setResumeOffset($resumeOffset);
			}
			else
			{				
				// create destination directory if doesn't already exist
				$res = self::createAndSetDir(dirname($fileDestination));
				if ( !$res )
				{
					KalturaLog::err('Cannot create destination directory ['.dirname($fileDestination).']');
					return false;
				}
			}
				
			KalturaLog::debug("Executing curl for downloading file at [$sourceUrl]");
			$res = $this->curlWrapper->exec($sourceUrl, $fileDestination); // download file
			$curlError = $this->curlWrapper->getError();
			$curlErrorNumber = $this->curlWrapper->getErrorNumber();
			
			//If we run mutiple file sync import using the same curl we need to reset the offset each time before fetching the file
			$this->curlWrapper->setResumeOffset(0);

			KalturaLog::debug("Curl results: $res");
	
			// handle errors
			if (!$res || $curlError)
			{
				if($curlErrorNumber != CURLE_OPERATION_TIMEOUTED)
				{
					// an error other than timeout occured  - cannot continue (timeout is handled with resuming)
					KalturaLog::err("$curlError");
					return false;
				}
				else
				{
					// timeout error occured, ignore and try to resume
					KalturaLog::debug('Curl timeout');
				}
			}
			
			if(!file_exists($fileDestination))
			{
				// destination file does not exist for an unknown reason
				KalturaLog::err("output file doesn't exist");
				return false;
			}
	
			clearstatcache();
			$actualFileSize = kFile::fileSize($fileDestination);
			if($actualFileSize == $resumeOffset)
			{
				// no downloading was done at all - error
				KalturaLog::err("$curlError");
				return false;
			}
			
			if($fileSize && $actualFileSize < $fileSize)
			{
				// part of file was downloaded - resume
				$resumeOffset = $actualFileSize;
				continue;
			}
			break;
		}

		KalturaLog::debug('File downloaded completely - will now check if done...');
		
		// file downloaded completely - check it
		return $this->checkFile($fileDestination, $fileSize);
	}

	static protected function parseMultiPart($contentType, $contents)
	{
		if (!kString::beginsWith($contentType, 'multipart/form-data; boundary='))
		{
			KalturaLog::err("failed to parse multipart content type [$contentType]");
			return false;
		}
	
		$explodedContentType = explode('=', $contentType);
		$boundary = trim($explodedContentType[1]);
	
		$result = array();
		$curPos = 0;
		for (;;)
		{
			if (substr($contents, $curPos, 2 + strlen($boundary)) != '--' . $boundary)
			{
				KalturaLog::err("expected [--$boundary] at pos [$curPos]");
				return false;
			}
				
			$headerEndPos = strpos($contents, "\n\n", $curPos);
			if ($headerEndPos === false)
			{
				break;
			}
			$headerEndPos += 2;		// skip the 2 newlines
				
			$dataEndPos = strpos($contents, "\n--" . $boundary, $headerEndPos);
			if ($dataEndPos === false)
			{
				KalturaLog::err("failed to find end boundary");
				return false;
			}
				
			$headers = explode("\n", substr($contents, $curPos, $headerEndPos - $curPos));
				
			$name = null;
			foreach ($headers as $header)
			{
				if (!kString::beginsWith($header, 'Content-Disposition: form-data; name="'))
				{
					continue;
				}
	
				$explodedHeader = explode('"', $header);
				$name = $explodedHeader[1];
			}
				
			if (is_null($name))
			{
				KalturaLog::err("failed to extract part name from " . print_r($headers, true));
				return false;
			}
				
			$result[$name] = substr($contents, $headerEndPos, $dataEndPos - $headerEndPos);
				
			$curPos = $dataEndPos + 1;
		}
	
		if (substr($contents, $curPos + 2 + strlen($boundary), 2) != '--')
		{
			KalturaLog::err("last boundary must end with --");
			return false;
		}
	
		return $result;
	}
	
	protected function fetchMultiFiles($fileSyncs, $baseUrl, $dcSecret)
	{
		$fileSyncIds = array();
		foreach ($fileSyncs as $fileSync)
		{
			$fileSyncIds[] = $fileSync->originalId;
		}
		$sourceUrl = self::getMultiSourceUrl(
				implode(',', $fileSyncIds),
				$baseUrl,
				$dcSecret);
		
		$contents = $this->curlWrapper->exec($sourceUrl);
		$curlError = $this->curlWrapper->getError();
		
		if ($contents === false || $curlError)
		{
			KalturaLog::err("failed to fetch $sourceUrl - $curlError");
			return false;
		}
		
		$contentType = curl_getinfo($this->curlWrapper->ch, CURLINFO_CONTENT_TYPE);
		
		$parsedContent = self::parseMultiPart($contentType, $contents);
		if ($parsedContent === false)
		{
			KalturaLog::err("failed to parse multipart response $sourceUrl");
			return false;
		}
		
		foreach ($fileSyncs as $fileSync)
		{
			if (!isset($parsedContent[$fileSync->originalId]))
			{
				KalturaLog::err("missing content for file " . $fileSync->originalId);
				continue;
			}
				
			$data = $parsedContent[$fileSync->originalId];
			$filePath = self::getFullPath($fileSync);
				
			$res = self::createAndSetDir(dirname($filePath));
			if (!$res)
			{
				KalturaLog::err("failed to create dir for $filePath");
				continue;
			}
				
			if (!file_put_contents($filePath, $data))
			{
				KalturaLog::err("failed to write file $filePath");
				continue;
			}
			
			clearstatcache();
				
			if ($this->checkFile($filePath, $fileSync->fileSize))
			{
				$this->markFileSyncAsReady($fileSync);
			}
		}
		
		return true;
	}
	
	/**
	 * Checks downloaded file.
	 * Changes the file mode and owner if required.
	 * 
	 * @param string $destFile
	 * @param int $fileSize
	 */
	private function checkFile($destFile, $fileSize = null)
	{
		KalturaLog::debug("checkFile($destFile, $fileSize)");

		if(!file_exists($destFile))
		{
			// destination file does not exist
			KalturaLog::err("file [$destFile] doesn't exist");
			return false;
		}

		$actualSize = kFile::fileSize($destFile);
		if(!$fileSize)
		{
			$fileSize = $actualSize;
		}
		else if($actualSize != $fileSize)
		{
			// destination file size is wrong
			KalturaLog::err("file [$destFile] has a wrong size. file size: [$actualSize] should be [$fileSize]");
			return false;
		}
			
		// set file owner
		$chown_name = self::$taskConfig->params->fileOwner;
		if ($chown_name) 
		{
			KalturaLog::debug("Changing owner of file [$destFile] to [$chown_name]");
			@chown($destFile, $chown_name);
		}
		
		// set file mode
		$chmod_perm = octdec(self::$taskConfig->params->fileChmod);
		if (!$chmod_perm) 
		{
			$chmod_perm = 0644;
		}
		KalturaLog::debug("Changing mode of file [$destFile] to [$chmod_perm]");
		@chmod($destFile, $chmod_perm);

		// IMPORTANT - check's if file is seen by apache
		if(!$this->checkFileExists($destFile, $fileSize))
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Fetches the header for the given $url
	 * @param string $url
	 * @return false|KCurlHeaderResponse
	 */
	private function fetchHeader($url)
	{
		KalturaLog::debug('Fetching header for ['.$url.']');
		
		// fetch the http headers
		$curlHeaderResponse = $this->curlWrapper->getHeader($url);
		$curlError = $this->curlWrapper->getError();
		$curlErrorNumber = $this->curlWrapper->getErrorNumber();
		
		if(!$curlHeaderResponse || !count($curlHeaderResponse->headers))
		{
			// error fetching headers
			KalturaLog::err("$curlError");
			return false;
		}
	
    	if($curlError)
    	{
    		KalturaLog::err("Headers error: $curlError");
    		KalturaLog::err("Headers error number: $curlErrorNumber");
    	}
    			
		if(!$curlHeaderResponse->isGoodCode())
		{
			// some error exists in the response
			KalturaLog::err('HTTP Error: ' . $curlHeaderResponse->code . ' ' . $curlHeaderResponse->codeName);
			return false;
		}
		
		// header fetched successfully - return it
		return $curlHeaderResponse;
	}
	
	/**
	 * Try to get the filesize from the given header
	 * @param KCurlHeaderResponse $curlHeaderResponse
	 * @return false|int file size or false on error
	 */
	private function getFilesizeFromHeader($curlHeaderResponse)
	{
		// try to get file size from headers
		if (isset($curlHeaderResponse->headers['content-length']))
		{
			return $curlHeaderResponse->headers['content-length'];
		}
		return false;
	}
	
	/**
	 * Check if the given curl header response contains a File-Sync-Type header == 'dir'
	 * @param KCurlHeaderResponse $curlHeaderResponse
	 * @return bool true/false
	 */
	private function isDirectoryHeader($curlHeaderResponse)
	{
		if (isset($curlHeaderResponse->headers['file-sync-type'])) 
		{
			if (trim($curlHeaderResponse->headers['file-sync-type']) === 'dir') 
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Create a new directory with the given $dirPath and changing its owner and mode according to the batch worker parameters
	 * @param string $dirPath path for the new directory
	 * @return bool true on success, false otherwise
	 */
	private function createAndSetDir($dirPath)
	{
		// create directory if does not exist
		KalturaLog::debug('Creating new directory ['.$dirPath.']');

		$res = self::createDirRecursive( $dirPath );
		if (!$res) 
		{
			return false;
		}
				
		// set directory owner
		$chown_name = self::$taskConfig->params->fileOwner;
		if ($chown_name) 
		{
			KalturaLog::debug("Changing owner of directory [$dirPath] to [$chown_name]");
			@chown($dirPath, $chown_name);
		}
		
		// set directory mode
		$chmod_perm = octdec(self::$taskConfig->params->fileChmod);
		if (!$chmod_perm) 
		{
			$chmod_perm = 0644;
		}
		KalturaLog::debug("Changing mode of directory [$dirPath] to [$chmod_perm]");
		@chmod($dirPath, $chmod_perm);
		
		return true;
	}
	
	/**
	 * Recursivly create directories for the given $dirPath
	 * @param string $dirPath
	 */
	private function createDirRecursive($dirPath)
	{
		if (!$dirPath)
		{
			return false;
		}
		
		if (is_dir($dirPath))
		{
			return true;
		}		
		
		if (is_dir(dirname($dirPath)))
		{
			// parent directory exists
			return $this->createDir($dirPath);
		}
		else
		{
			// parent directory does not exist
			$res = $this->createDirRecursive(dirname($dirPath));
			return $res && $this->createDir($dirPath);
		}
	}
}
