<?php
/**
 * @package plugins.multiCenters
 * @subpackage model.data
 */
class kFileSyncImportJobData extends kJobData
{
	
	/** MEMBER VARIABLES **/
	
	/**
	 * Full URL to file on source data center.
	 * @var string
	 */
	private $sourceUrl;
	
	/**
	 * File sync ID number
	 * @var string
	 */
	private $filesyncId;
	
	/**
	 * Temporary path to download file to
	 * @var string
	 */
	private $tmpFilePath;
	
	/**
	 * Path to file's final target destination.
	 * @var string
	 */
	private $destFilePath;
	
	
	
	/** GETTERS & SETTERS **/
	
	
	/**
	 * @return the $sourceUrl
	 */
	public function getSourceUrl()
	{
		return $this->sourceUrl;
	}
	
	/**
	 * 
	 * @param string $sourceUrl the $sourceUrl to set
	 */
	public function setSourceUrl($sourceUrl)
	{
		$this->sourceUrl = $sourceUrl;
	}
	
	/**
	 * @return the $filesyncId
	 */
	public function getFilesyncId()
	{
		return $this->filesyncId;
	}
	
	/**
	 * @param string $fileSyncId the $fileSyncId to set
	 */
	public function setFilesyncId($fileSyncId)
	{
		$this->filesyncId = $fileSyncId;
	}
	
	/**
	 * @return the $tmpFilePath
	 */
	public function getTmpFilePath()
	{
		return $this->tmpFilePath;
	}
	
	/**
	 * @param string $tmpFilePath the $tmpFilePath to set
	 */
	public function setTmpFilePath($tmpFilePath)
	{
		$this->tmpFilePath = $tmpFilePath;
	}
	
	/**
	 * @return the $destFilePath
	 */
	public function getDestFilePath()
	{
		return $this->destFilePath;
	}
	
	/**
	 * @param string $destFilePath the $destFilePath to set
	 */
	public function setDestFilePath($destFilePath)
	{
		$this->destFilePath = $destFilePath;
	}
	
	
}
