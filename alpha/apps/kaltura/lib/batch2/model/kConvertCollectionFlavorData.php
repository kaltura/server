<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kConvertCollectionFlavorData extends kJobData
{
	/**
	 * @var string
	 */
	private $flavorAssetId;
	
	/**
	 * @var int
	 */
	private $flavorParamsOutputId;
	
	/**
	 * @var int
	 */
	private $readyBehavior;
	
	/**
	 * @var int
	 */
	private $videoBitrate;
	
	/**
	 * @var int
	 */
	private $audioBitrate;
	
	/**
	 * @var string
	 */
	private $destFileSyncLocalPath;
	
	/**
	 * @var string
	 */
	private $destFileSyncRemoteUrl;
	
	/**
	 * @return the $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}

	/**
	 * @return the $destFileSyncLocalPath
	 */
	public function getDestFileSyncLocalPath()
	{
		return $this->destFileSyncLocalPath;
	}

	/**
	 * @return the $destFileSyncRemoteUrl
	 */
	public function getDestFileSyncRemoteUrl()
	{
		return $this->destFileSyncRemoteUrl;
	}

	/**
	 * @param $flavorAssetId the $flavorAssetId to set
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}

	/**
	 * @param $destFileSyncLocalPath the $destFileSyncLocalPath to set
	 */
	public function setDestFileSyncLocalPath($destFileSyncLocalPath)
	{
		$this->destFileSyncLocalPath = $destFileSyncLocalPath;
	}

	/**
	 * @param $destFileSyncRemoteUrl the $destFileSyncRemoteUrl to set
	 */
	public function setDestFileSyncRemoteUrl($destFileSyncRemoteUrl)
	{
		$this->destFileSyncRemoteUrl = $destFileSyncRemoteUrl;
	}
	
	/**
	 * @return the $flavorParamsOutputId
	 */
	public function getFlavorParamsOutputId()
	{
		return $this->flavorParamsOutputId;
	}

	/**
	 * @return the $readyBehavior
	 */
	public function getReadyBehavior()
	{
		return $this->readyBehavior;
	}

	/**
	 * @return the $videoBitrate
	 */
	public function getVideoBitrate()
	{
		return $this->videoBitrate;
	}

	/**
	 * @return the $audioBitrate
	 */
	public function getAudioBitrate()
	{
		return $this->audioBitrate;
	}

	/**
	 * @param $flavorParamsOutputId the $flavorParamsOutputId to set
	 */
	public function setFlavorParamsOutputId($flavorParamsOutputId)
	{
		$this->flavorParamsOutputId = $flavorParamsOutputId;
	}

	/**
	 * @param $readyBehavior the $readyBehavior to set
	 */
	public function setReadyBehavior($readyBehavior)
	{
		$this->readyBehavior = $readyBehavior;
	}

	/**
	 * @param $videoBitrate the $videoBitrate to set
	 */
	public function setVideoBitrate($videoBitrate)
	{
		$this->videoBitrate = $videoBitrate;
	}

	/**
	 * @param $audioBitrate the $audioBitrate to set
	 */
	public function setAudioBitrate($audioBitrate)
	{
		$this->audioBitrate = $audioBitrate;
	}
}
