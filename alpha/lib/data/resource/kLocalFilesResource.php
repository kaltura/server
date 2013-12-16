<?php
/**
 * Used to ingest media files that is already accessible on the shared disc.
 *
 * @package Core
 * @subpackage model.data
 */
class kLocalFilesResource extends kContentResource
{
	/**
	 * Full path to the local files 
	 * @var array
	 */
	private $localFilePaths;
	
	/**
	 * @var bool
	 */
	private $keepOriginalFile = true;
	
	/**
	 * @var int
	 */
	private $sourceType;
	
	/**
	 * @return array
	 */
	public function getLocalFilePaths()
	{
		return $this->localFilePaths;
	}

	/**
	 * @param array $localFilePaths
	 */
	public function setLocalFilePaths(array $localFilePaths)
	{
		$this->localFilePath = $localFilePaths;
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
}