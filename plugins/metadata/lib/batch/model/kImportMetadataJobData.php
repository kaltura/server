<?php

/** 
 * @package Core
 * @subpackage Batch
 */
class kImportMetadataJobData
{
	/**
	 * @var string
	 */
	private $srcFileUrl;
	
	/**
	 * @var string
	 */
	private $destFileLocalPath;
	
	
	/**
	 * @var int
	 */
	private $metadataId;
	
	
	/**
	 * @return the $srcFileUrl
	 */
	public function getSrcFileUrl()
	{
		return $this->srcFileUrl;
	}

	/**
	 * @return the $destFileLocalPath
	 */
	public function getDestFileLocalPath()
	{
		return $this->destFileLocalPath;
	}

	/**
	 * @param $srcFileUrl the $srcFileUrl to set
	 */
	public function setSrcFileUrl($srcFileUrl)
	{
		$this->srcFileUrl = $srcFileUrl;
	}

	/**
	 * @param $destFileLocalPath the $destFileLocalPath to set
	 */
	public function setDestFileLocalPath($destFileLocalPath)
	{
		$this->destFileLocalPath = $destFileLocalPath;
	}
	
	/**
	 * @return the $metadataId
	 */
	public function getMetadataId()
	{
		return $this->metadataId;
	}

	/**
	 * @param $metadataId the $metadataId to set
	 */
	public function setMetadataId($metadataId)
	{
		$this->metadataId = $metadataId;
	}

}

?>