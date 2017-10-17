<?php
/**
 * Used to ingest media file that is already accessible on the shared disc.
 *
 * @package Core
 * @subpackage model.data
 */
class kLocalFileResource extends kContentResource
{
	/**
	 * Full path to the local file 
	 * @var string
	 */
	private $localFilePath;
	
	/**
	 * @var bool
	 */
	private $keepOriginalFile = false;
	
	/**
	 * @var int
	 */
	private $sourceType = entry::ENTRY_MEDIA_SOURCE_FILE;
	
	/**
	 * @var bool
	 */
	private $isReady = true;
	
	/**
	 * @return string
	 */
	public function getLocalFilePath()
	{
		return $this->localFilePath;
	}

	/**
	 * @param string $localFilePath
	 */
	public function setLocalFilePath($localFilePath)
	{
		$this->localFilePath = $localFilePath;
	}
	
	/**
	 * @return the $keepOriginalFile
	 */
	public function getKeepOriginalFile()
	{
		return $this->keepOriginalFile;
	}

	/**
	 * @param bool $keepOriginalFile
	 */
	public function setKeepOriginalFile($keepOriginalFile)
	{
		$this->keepOriginalFile = $keepOriginalFile;
	}
	
	/**
	 * @return the $sourceType
	 */
	public function getSourceType()
	{
		return $this->sourceType;
	}

	/**
	 * @param int $sourceType
	 */
	public function setSourceType($sourceType)
	{
		$this->sourceType = $sourceType;
	}
	
	/**
	 * @return the $isReady
	 */
	public function getIsReady()
	{
		return $this->isReady;
	}

	/**
	 * @param bool $isReady
	 */
	public function setIsReady($isReady)
	{
		$this->isReady = $isReady;
	}

	/**
	 * @param BaseObject $object
	 */
	public function attachCreatedObject(BaseObject $object)
	{
	}

	public function getMediaType()
	{
		return null;
 	}
}