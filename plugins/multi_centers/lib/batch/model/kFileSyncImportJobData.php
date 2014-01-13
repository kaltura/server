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
	
	/**
	 * File size
	 * @var int
	 */
	private $fileSize;
	
	/**
 	* Is the asset being synced the entries source asset
 	* @var bool
 	*/
	private $isSourceAsset = false;	
	
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
	
	/**
	 * @param int $fileSize the $fileSize to set
	 */
	public function setFileSize($fileSize)
	{
		$this->fileSize = $fileSize;
	}
	
	/**
	 * @return the $fileSize
	 */
	public function getFileSize()
	{
		return $this->fileSize;
	}
	
	/** 
 	* @param bool $isSourceAsset the $isSourceAsset to set
 	*/
	public function setIsSourceAsset($isSourceAsset)
	{
  		$this->isSourceAsset = $isSourceAsset;
	}

	/**
 	* @return the $isSourceAsset
 	*/
	public function getIsSourceAsset()
	{
  		return $this->isSourceAsset;
	}
	
	public function calculateEstimatedEffort(BatchJob $batchJob) {
		return $this->fileSize;
	}
	
	/**
	 * This function calculates the urgency of the job according to its data
	 * @param BatchJob $batchJob
	 * @return integer the calculated urgency
	 */
	public function calculateUrgency(BatchJob $batchJob) {		
	  	//In case the asset currently being synced is source we lower its urgency by 1 to process the job faster 
	  	if($this->isSourceAsset)
	    	$urgency = BatchJobUrgencyType::FILE_SYNC_SOURCE;
	  	else 
	    	$urgency = BatchJobUrgencyType::FILE_SYNC_NOT_SOURCE;
	    
	  	return $urgency;
	}
	
	/**
	 * This function calculates the priority of the job.
	 * @param BatchJob $batchJob
	 * @return integer the calculated priority
	 */
	public function calculatePriority(BatchJob $batchJob) {
	  	$priority = parent::calculatePriority($batchJob);
	  
	  	if($this->isSourceAsset)
	    	$priority = $priority - 1;
	    
	  	return $priority;
	}
}
