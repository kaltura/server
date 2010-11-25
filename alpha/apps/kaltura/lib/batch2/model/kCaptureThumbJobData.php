<?php

/**
 *  
 * @package Core
 * @subpackage Batch
 */
class kCaptureThumbJobData extends kJobData
{
	/**
	 * @var string
	 */
	private $srcFileSyncLocalPath;
	
	/**
	 * The translated path as used by the scheduler
	 * @var string
	 */
	private $actualSrcFileSyncLocalPath;
	
	/**
	 * @var string
	 */
	private $srcFileSyncRemoteUrl;
	
	/**
	 * @var int
	 */
	private $thumbParamsOutputId;
	
	/**
	 * @var thumbParamsOutput
	 */
	private $thumbParamsOutput;
	
	/**
	 * @var string
	 */
	private $thumbAssetId;
	
	/**
	 * @var KalturaAssetType
	 */
	private $srcAssetType;
	
	/**
	 * @return the $srcAssetType
	 */
	public function getSrcAssetType()
	{
		return $this->srcAssetType;
	}

	/**
	 * @param $srcAssetType the $srcAssetType to set
	 */
	public function setSrcAssetType($srcAssetType)
	{
		$this->srcAssetType = $srcAssetType;
	}

	/**
	 * @return the $thumbAssetId
	 */
	public function getThumbAssetId()
	{
		return $this->thumbAssetId;
	}

	/**
	 * @param $thumbAssetId the $thumbAssetId to set
	 */
	public function setThumbAssetId($thumbAssetId)
	{
		$this->thumbAssetId = $thumbAssetId;
	}

	/**
	 * @return the $srcFileSyncLocalPath
	 */
	public function getSrcFileSyncLocalPath()
	{
		return $this->srcFileSyncLocalPath;
	}
	
	/**
	 * @param $srcFileSyncRemoteUrl the $srcFileSyncRemoteUrl to set
	 */
	public function setSrcFileSyncRemoteUrl($srcFileSyncRemoteUrl)
	{
		$this->srcFileSyncRemoteUrl = $srcFileSyncRemoteUrl;
	}

	/**
	 * @return the $srcFileSyncRemoteUrl
	 */
	public function getSrcFileSyncRemoteUrl()
	{
		return $this->srcFileSyncRemoteUrl;
	}

	/**
	 * @param $thumbParamsOutput the $thumbParamsOutput to set
	 */
	public function setThumbParamsOutput($thumbParamsOutput)
	{
		$this->thumbParamsOutput = $thumbParamsOutput;
	}

	/**
	 * @param $srcFileSyncLocalPath the $srcFileSyncLocalPath to set
	 */
	public function setSrcFileSyncLocalPath($srcFileSyncLocalPath)
	{
		$this->srcFileSyncLocalPath = $srcFileSyncLocalPath;
	}

	/**
	 * @return the $actualSrcFileSyncLocalPath
	 */
	public function getActualSrcFileSyncLocalPath()
	{
		return $this->actualSrcFileSyncLocalPath;
	}

	/**
	 * @param $actualSrcFileSyncLocalPath the $actualSrcFileSyncLocalPath to set
	 */
	public function setActualSrcFileSyncLocalPath($actualSrcFileSyncLocalPath)
	{
		$this->actualSrcFileSyncLocalPath = $actualSrcFileSyncLocalPath;
	}
	
	/**
	 * @param $thumbParamsOutputId the $thumbParamsOutputId to set
	 */
	public function setThumbParamsOutputId($thumbParamsOutputId)
	{
		$this->thumbParamsOutputId = $thumbParamsOutputId;
	}

	/**
	 * @return thumbParamsOutput the $thumbParamsOutput
	 */
	public function getThumbParamsOutput()
	{
		return $this->thumbParamsOutput;
	}

	/**
	 * @return int the $thumbParamsOutputId
	 */
	public function getThumbParamsOutputId()
	{
		return $this->thumbParamsOutputId;
	}

	/**
	 * @return the ready behavior
	 */
	public function getReadyBehavior()
	{
		$thumbParamsOutput = thumbParamsOutputPeer::retrieveByPK($this->thumbParamsOutputId);
		if($thumbParamsOutput)
			return $thumbParamsOutput->getReadyBehavior();
			
		return null;
	}
}
