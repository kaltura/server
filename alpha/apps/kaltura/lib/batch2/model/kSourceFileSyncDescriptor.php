<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kSourceFileSyncDescriptor extends kFileSyncDescriptor
{	
	/**
	 * The translated path as used by the scheduler
	 * @var string
	 */
	private $actualFileSyncLocalPath;
	
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
	 * @return the $actualFileSyncLocalPath
	 */
	public function getActualFileSyncLocalPath() 
	{
		return $this->actualFileSyncLocalPath;
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
	 * @param string $actualFileSyncLocalPath
	 */
	public function setActualFileSyncLocalPath($actualFileSyncLocalPath) 
	{
		$this->actualFileSyncLocalPath = $actualFileSyncLocalPath;
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
}