<?php
/**
 * Created by IntelliJ IDEA.
 * User: inbal.bendavid
 * Date: 6/20/2019
 * Time: 4:55 PM
 */

class kS3UploadTokenMgr extends kBaseUploadTokenMgr
{

	const MIN_PART_SIZE = 5242880;
	const MAX_PART_SIZE = 5368709120;

	protected static $s3FileSystemManager;


	public function __construct(UploadToken $uploadToken, $finalChunk = true)
	{
		KalturaLog::info("Init for upload token id [{$uploadToken->getId()}]");
		parent::__construct($uploadToken, $finalChunk);
	}

	/**
	 * Resume the upload token with the uploaded file optionally at a given offset
	 *
	 * @param file $fileData
	 * @param float $resumeAt
	 * @return string
	 * @throws kUploadTokenException
	 */
	protected function handleResume($fileData, $resumeAt)
	{
		$minimumChunkSize = $this->_uploadToken->getMinimumChunkSize();
		if($minimumChunkSize && $minimumChunkSize >= self::MIN_PART_SIZE)
		{
			return $this->handleOptimizedResume($fileData, $resumeAt);
		}
	}


	/**
	 * handle resume when all the chunks are suitable for S3 multipartUpload without the need of inner concatenation
	 *
	 * @param $fileData
	 * @param $resumeAt
	 * @return string
	 * @throws kUploadTokenException
	 */
	protected function handleOptimizedResume($fileData, $resumeAt)
	{
		$uploadFilePath = $this->_uploadToken->getUploadTempPath();
		$sourceFilePath = $fileData['tmp_name'];
		$chunkSize = kFile::fileSize($sourceFilePath);
		self::$s3FileSystemManager = kSharedFileSystemMgr::getInstance();

		if ($resumeAt != -1) // this may not be a sequential chunk added at the end of the file
		{
			// support backwards compatibility of overriding a final chunk at the offset zero
			$verifyFinalChunk = $this->_finalChunk && $resumeAt > 0;
			// if this is the final chunk the expected file size would be the resume position + the last chunk size
			$expectedFileSize = $verifyFinalChunk ? ($resumeAt + $chunkSize) : 0;
			$chunkFilePath = "$uploadFilePath.chunk.$resumeAt";
			if($this->_autoFinalize && $this->checkIsFinalChunk($chunkSize))
			{
				$verifyFinalChunk = true;
				$expectedFileSize = $this->_uploadToken->getFileSize();
			}
			$uploadFinalChunkMaxAppendTime = kConf::get('upload_final_chunk_max_append_time', 'local', 30);
			$this->uploadChunk($chunkFilePath, $this->_uploadToken, $resumeAt, $chunkSize);
			// if final chunk need to complete the upload
		}
		else
		{
			// add sequentially at the end of file
			$currentFileSize = $this->_uploadToken->getLastFileSize();
			$chunkFilePath = "$uploadFilePath.chunk.$currentFileSize";
			$this->uploadChunk($chunkFilePath, $this->_uploadToken, $this->_uploadToken->getLastFileSize(), $chunkSize);

			if($this->_autoFinalize && $this->_uploadToken->getFileSize() >= $currentFileSize)
			{
				$this->_finalChunk = true;
			}

			if($this->_finalChunk)
			{
				$this->createFullFile();
			}
		}
		return $currentFileSize;
	}

	/**
	 * upload chunk to s3
	 *
	 * @param $filePath
	 * @param $uploadToken
	 * @param $resumeAt
	 * @param $chunkSize
	 */
	protected function uploadChunk($filePath, $uploadToken, $resumeAt, $chunkSize)
	{
		$fileContent = kFile::getFileContent($filePath);
		$result = self::$s3FileSystemManager->putFileContent($filePath, (string)$fileContent);
		if($result)
		{
			$chunkEtag = $result['ETag'];
			$this->addUploadedPart($uploadToken, $resumeAt, $chunkEtag);
			$uploadedSize = $resumeAt + $chunkSize;
			$this->_uploadToken->setLastFileSize($uploadedSize);
		}

	}

	/**
	 * add new part to upload token parts list
	 *
	 * @param $uploadToken
	 * @param $resumeAt
	 * @param $chunkEtag
	 */
	protected function addUploadedPart($uploadToken, $resumeAt, $chunkEtag)
	{
		$parts = $uploadToken->getParts();
		$parts[$resumeAt] = $chunkEtag;
		$uploadToken->setParts($parts);
	}

	/**
	 *
	 * @param $finalFilePath
	 * @param $uploadToken
	 */
	protected function createFullFile($finalFilePath, $uploadToken)
	{
		$uploadId = self::$s3FileSystemManager->doCreateMultipartUpload($finalFilePath);
		$uploadParts = $uploadToken->getParts();
		$copiedParts = array();
		$partNumber = 1;
		foreach ($uploadParts as $time => $eTag)
		{
			$result = self::$s3FileSystemManager->doMultipartCopyPart($uploadId, $partNumber, $eTag, $finalFilePath);
			if($result)
			{
				$copiedParts['Parts'][$partNumber] = array(
					'PartNumber' => $partNumber,
					'ETag' => $result['ETag'],
				);
			}
		}
		self::$s3FileSystemManager->doCompleteMultipartUpload($finalFilePath, $uploadId, $copiedParts);
	}


}