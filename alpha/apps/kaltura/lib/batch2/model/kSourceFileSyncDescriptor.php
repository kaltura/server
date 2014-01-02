<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kSourceFileSyncDescriptor 
{
	/**
	 * @var string
	 */
	private $fileSyncLocalPath;
	
	/**
	 * The translated path as used by the scheduler
	 * @var string
	 */
	private $actualFileSyncLocalPath;
	
	/**
	 * @var string
	 */
	private $fileSyncRemoteUrl;
	
	/**
	 * 
	 * @var string
	 */
	private $assetId;
	
	/**
	 * 
	 * @var int
	 */
	private $assetParamsId;
	
	/**
	 * 
	 * @var int
	 */
	private $fileSyncObjectSubType;
	
	/**
	 * @return the $fileSyncLocalPath
	 */
	public function getFileSyncLocalPath() 
	{
		return $this->fileSyncLocalPath;
	}

	/**
	 * @return the $actualFileSyncLocalPath
	 */
	public function getActualFileSyncLocalPath() 
	{
		return $this->actualFileSyncLocalPath;
	}

	/**
	 * @return the $fileSyncRemoteUrl
	 */
	public function getFileSyncRemoteUrl() 
	{
		return $this->fileSyncRemoteUrl;
	}

	/**
	 * @return the $assetId
	 */
	public function getAssetId() 
	{
		return $this->assetId;
	}

	/**
	 * @return the $assetParamsId
	 */
	public function getAssetParamsId() 
	{
		return $this->assetParamsId;
	}

	/**
	 * @param string $fileSyncLocalPath
	 */
	public function setFileSyncLocalPath($fileSyncLocalPath) 
	{
		$this->fileSyncLocalPath = $fileSyncLocalPath;
	}

	/**
	 * @param string $actualFileSyncLocalPath
	 */
	public function setActualFileSyncLocalPath($actualFileSyncLocalPath) 
	{
		$this->actualFileSyncLocalPath = $actualFileSyncLocalPath;
	}

	/**
	 * @param string $fileSyncRemoteUrl
	 */
	public function setFileSyncRemoteUrl($fileSyncRemoteUrl) 
	{
		$this->fileSyncRemoteUrl = $fileSyncRemoteUrl;
	}

	/**
	 * @param string $assetId
	 */
	public function setAssetId($assetId) 
	{
		$this->assetId = $assetId;
	}

	/**
	 * @param int $assetParamsId
	 */
	public function setAssetParamsId($assetParamsId) 
	{
		$this->assetParamsId = $assetParamsId;
	}
	
	/**
	 * @return the $fileSyncObjectSubType
	 */
	public function getFileSyncObjectSubType() {
		return $this->fileSyncObjectSubType;
	}

	/**
	 * @param int $fileSyncObjectSubType
	 */
	public function setFileSyncObjectSubType($fileSyncObjectSubType) {
		$this->fileSyncObjectSubType = $fileSyncObjectSubType;
	}	
}