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
}