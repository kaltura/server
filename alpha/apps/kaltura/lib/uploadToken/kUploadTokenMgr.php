<?php
class kUploadTokenMgr extends kBaseUploadTokenMgr
{

	/**
	 * kUploadTokenMgr constructor.
	 * @param UploadToken $uploadToken
	 * @param bool $finalChunk
	 */
	public function __construct(UploadToken $uploadToken, $finalChunk = true)
	{
		KalturaLog::info("Init for upload token id [{$uploadToken->getId()}]");
		parent::__construct($uploadToken, $finalChunk);
	}

	/**
	 * Resume the upload token with the uploaded file optionally at a given offset
	 *
	 * @param $fileData
	 * @param $resumeAt
	 * @return bool|int
	 * @throws kUploadTokenException
	 */
	protected function handleResume($fileData, $resumeAt)
	{
		$uploadFilePath = $this->_uploadToken->getUploadTempPath();
		if (!file_exists($uploadFilePath))
			throw new kUploadTokenException("Temp file [$uploadFilePath] was not found when trying to resume", kUploadTokenException::UPLOAD_TOKEN_FILE_NOT_FOUND_FOR_RESUME);
		
		$sourceFilePath = $fileData['tmp_name'];
		
		if ($resumeAt != -1) // this may not be a sequential chunk added at the end of the file
		{
			// support backwards compatibility of overriding a final chunk at the offset zero  
			$verifyFinalChunk = $this->_finalChunk && $resumeAt > 0;
			
			// if this is the final chunk the expected file size would be the resume position + the last chunk size
			$chunkSize = filesize($sourceFilePath);
			$expectedFileSize = $verifyFinalChunk ? ($resumeAt + $chunkSize) : 0;

			$chunkFilePath = "$uploadFilePath.chunk.$resumeAt";
			$succeeded = rename($sourceFilePath, $chunkFilePath);
			
			if($this->_autoFinalize && $this->checkIsFinalChunk($chunkSize) && $succeeded)
			{
				$verifyFinalChunk = true;
				$expectedFileSize = $this->_uploadToken->getFileSize();
			}
			
			$uploadFinalChunkMaxAppendTime = kConf::get('upload_final_chunk_max_append_time', 'local', 30);
			
			// if finalChunk, try appending chunks till reaching expected file size for up to 30 seconds while sleeping for 1 second each iteration
			$count = 0;
			do {
				if ($count ++)
					Sleep(1);
				
				$currentFileSize = self::appendAvailableChunks($uploadFilePath);
				KalturaLog::log("handleResume iteration: $count chunk: $chunkFilePath size: $chunkSize finalChunk: {$this->_finalChunk} filesize: $currentFileSize expected: $expectedFileSize");
			} while ($verifyFinalChunk && $currentFileSize != $expectedFileSize && $count < $uploadFinalChunkMaxAppendTime);

			if ($verifyFinalChunk && $currentFileSize != $expectedFileSize)
				throw new kUploadTokenException("final size $currentFileSize failed to match expected size $expectedFileSize", kUploadTokenException::UPLOAD_TOKEN_CANNOT_MATCH_EXPECTED_SIZE);
		} else {
			$uploadFileResource = fopen($uploadFilePath, 'r+b');
			fseek($uploadFileResource, 0, SEEK_END);
			
			self::appendChunk($sourceFilePath, $uploadFileResource);
			
			$currentFileSize = ftell($uploadFileResource);
			
			if($this->_autoFinalize && $this->_uploadToken->getFileSize() <= $currentFileSize)
				$this->_finalChunk = true;
			
			fclose($uploadFileResource);
		}
		
		return $currentFileSize; 
	}

	/**
	 * appends single chunk at the end of a file
	 *
	 * @param $sourceFilePath
	 * @param $targetFileResource
	 */
	static protected function appendChunk($sourceFilePath, $targetFileResource)
	{
		$sourceFileResource = fopen($sourceFilePath, 'rb');
		if(!$sourceFileResource)
		{
			KalturaLog::err("Could not open file [{$sourceFilePath}] for read");
			return;
		}
		
		while (! feof($sourceFileResource)) {
			$data = fread($sourceFileResource, 1024 * 100);
			fwrite($targetFileResource, $data);
		}
		
		fclose($sourceFileResource);
		unlink($sourceFilePath);
	}

	/**
	 * appends all available chunks at the end of a file
	 *
	 * @param $targetFilePath
	 * @return bool|int
	 */
	static protected function appendAvailableChunks($targetFilePath)
	{
		$targetFileResource = fopen($targetFilePath, 'r+b');
		
		fseek($targetFileResource, 0, SEEK_END);
		
		// use glob to find existing chunks and append ones which start within or at the end of the file and will increase its size
		// in order to prevent race conditions, rename the chunk to "{chunkname}.{random}.locked" before appending it   
		// the code should handle the following rare scenarios:
		// 1. parallel procesess trying to add the same chunk
		// 2. append failing half way and recovered by the client resneding the same chunk. The random part in the locked file name
		// will prevent the re-uploaded chunk from coliding with the failed one
		while (1) {
			$currentFileSize = ftell($targetFileResource);
			
			$validChunk = false;
			
			$globStart = microtime(true);
			$chunks = glob("$targetFilePath.chunk.*", GLOB_NOSORT);
			$globTook = (microtime(true) - $globStart);
			KalturaLog::debug("glob took - " . $globTook . " seconds");
						
			foreach($chunks as $nextChunk)
			{
				$parts = explode(".", $nextChunk);
				if (count($parts))
				{
					$chunkOffset = $parts[count($parts) - 1];
					if ($chunkOffset == "locked") // don't touch chunks that were locked and may have failed appending half way
						continue;
					
					// dismiss chunks which won't enlarge the file or which are starting after the end of the file
					// support backwards compatibility of overriding a final chunk at the offset zero
					if ($chunkOffset == 0 || ($chunkOffset <= $currentFileSize && $chunkOffset + filesize($nextChunk) > $currentFileSize))
					{
						fseek($targetFileResource, $chunkOffset, SEEK_SET);
						$validChunk = true;
						break; 
					}
					else
					{
						KalturaLog::log("ignoring chunk: $nextChunk offset: $chunkOffset fileSize: $currentFileSize");
					}
				}
			}
			
			if (!$validChunk)
				break;
			
			$lockedFile = "$nextChunk.".microtime(true).".locked";
			if (! rename($nextChunk, $lockedFile)) // another process is already appending this file
			{
				KalturaLog::log("rename($nextChunk, $lockedFile) failed");
				break;
			}
			
			self::appendChunk($lockedFile, $targetFileResource);
		}
		
		fclose($targetFileResource);
		
		return $currentFileSize;
	}

	/**
	 * Returns the target upload path for upload token id and extension
	 *
	 * @param $uploadTokenId
	 * @param string $extension
	 * @return string
	 */
	protected function getUploadPath($uploadTokenId, $extension = '')
	{
		if (!$extension)
			$extension = self::NO_EXTENSION_IDENTIFIER;

		return myContentStorage::getFSUploadsPath().substr($uploadTokenId, -2).'/'.$uploadTokenId.'.'.$extension;
	}

	protected function startFullFileUpload($uploadFilePath, $fileSize, $resumeAt)
	{
		return kFile::fileSize($this->_uploadToken->getUploadTempPath());
	}

	protected function closeFullFileUpload()
	{

	}

	protected function getFinalFilePath()
	{

	}
}
